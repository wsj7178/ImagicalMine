<?php

/*
 *
 *  _                       _           _ __  __ _
 * (_)                     (_)         | |  \/  (_)
 *  _ _ __ ___   __ _  __ _ _  ___ __ _| | \  / |_ _ __   ___
 * | | '_ ` _ \ / _` |/ _` | |/ __/ _` | | |\/| | | '_ \ / _ \
 * | | | | | | | (_| | (_| | | (_| (_| | | |  | | | | | |  __/
 * |_|_| |_| |_|\__,_|\__, |_|\___\__,_|_|_|  |_|_|_| |_|\___|
 *                     __/ |
 *                    |___/
 *
 * This program is a third party build by ImagicalMine.
 *
 * PocketMine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ImagicalMine Team
 * @link http://forums.imagicalcorp.ml/
 *
 *
*/

namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TeleportCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.tp.description",
			"%commands.tp.usage"
		);
		//todo check/add permissions subcommands
		$this->setPermission("pocketmine.command.teleport");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		$countArgs = count($args);

		if($countArgs < 1 or $countArgs > 6){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

			return true;
		}

		$target = null;

		if($countArgs === 1) {
            //check subcommands
		    switch($args[0]) {
		        case 'off':
		            //player disable teleporting to or from him
		            return true;
		            break;
		        case 'on':
		            //player enable teleporting to or from him
		            return true;
		            break;
		    }

		    if(false === ($sender instanceof Player)){
		        $sender->sendMessage(TextFormat::RED . "Please provide a player!");

		        return true;
		    }
		}

		if(in_array($countArgs, array(1,3))) {
		    //tp sender to somewhere
		    $originName = $sender->getName();
		    $origin = $sender;
		}elseif(in_array($countArgs, array(2,4,5,6))) {
		    //tp arg[0] to somewhere
		    $originName = $args[0];
		    $origin = $sender->getServer()->getPlayer($originName);
		}else{
		    //wrong
		    return true;
		}

		if(in_array($countArgs, array(1,2))) {
		    //tp to player
		    $targetName = $args[$countArgs-1];
		    $target = $sender->getServer()->getPlayer($targetName);
		    if(!($origin instanceof Player)){
		        $sender->sendMessage(TextFormat::RED . "Can't find player " . $originName);
		        return true;
		    }
		    if(!($target instanceof Player)){
		        $sender->sendMessage(TextFormat::RED . "Can't find player " . $targetName);
		        return true;
		    }
		    $origin->teleport($target);
		    Command::broadcastCommandMessage($sender, new TranslationContainer("commands.tp.success", array($origin->getName(), $target->getName())));

		}else{
		    //tp to position
		}

		//origin
/*
		if($countArgs === 1 or $countArgs === 3){
			if($sender instanceof Player){
				$target = $sender;
			}else{
				$sender->sendMessage(TextFormat::RED . "Please provide a player!");

				return true;
			}

			// check if arg is a subcommand or a player
			if($countArgs === 1){
				$target = $sender->getServer()->getPlayer($args[0]);
				if($target === null){
					$sender->sendMessage(TextFormat::RED . "Can't find player " . $args[0]);

					return true;
				}
			}
		}else{
			$target = $sender->getServer()->getPlayer($args[0]);
			if($target === null){
				$sender->sendMessage(TextFormat::RED . "Can't find player " . $args[0]);

				return true;
			}
			if($countArgs === 2){
				$origin = $target;
				$target = $sender->getServer()->getPlayer($args[1]);
				if($target === null){
					$sender->sendMessage(TextFormat::RED . "Can't find player " . $args[1]);

					return true;
				}
			}
		}

		if($countArgs < 3){
			$origin->teleport($target);
			Command::broadcastCommandMessage($sender, new TranslationContainer("commands.tp.success", [$origin->getName(), $target->getName()]));

			return true;
*/
		}elseif($target->getLevel() !== null){
			if($countArgs === 4 or $countArgs === 6){
				$pos = 1;
			}else{
				$pos = 0;
			}

			$x = $this->getRelativeDouble($target->x, $sender, $args[$pos++]);
			$y = $this->getRelativeDouble($target->y, $sender, $args[$pos++], 0, 128);
			$z = $this->getRelativeDouble($target->z, $sender, $args[$pos++]);
			$yaw = $target->getYaw();
			$pitch = $target->getPitch();

			if($countArgs === 6 or ($countArgs === 5 and $pos === 3)){
				$yaw = $args[$pos++];
				$pitch = $args[$pos++];
			}

			$target->teleport(new Vector3($x, $y, $z), $yaw, $pitch);
			Command::broadcastCommandMessage($sender, new TranslationContainer("commands.tp.success.coordinates", [$target->getName(), round($x, 2), round($y, 2), round($z, 2)]));

			return true;
		}

		$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));

		return true;
	}
}
