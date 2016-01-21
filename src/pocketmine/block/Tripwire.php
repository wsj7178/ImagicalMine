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

namespace pocketmine\block;


use pocketmine\item\Item;

use pocketmine\math\AxisAlignedBB;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\level\Level;

class Tripwire extends Flowable{

	protected $id = self::TRIPWIRE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function isSolid(){
		return false;
	}

	public function getName() : string{
		return "Tripwire";
	}

	public function getHardness() : int{
		return 0.1;
	}

	public function canPassThrough(){
		return true;
	}

	protected function recalculateBoundingBox(){
		if($this->getSide(Vector3::SIDE_DOWN) instanceof Transparent){
			return new AxisAlignedBB(
				$this->x,
				$this->y,
				$this->z,
				$this->x + 1,
				$this->y + 0.5,
				$this->z + 1
			);
		}
		else{
			return new AxisAlignedBB(
				$this->x,
				$this->y,
				$this->z,
				$this->x + 1,
				$this->y + 0.09375,
				$this->z + 1
			);
		}
	}

	public function getDrops(Item $item) : array{
		$drops = [];
		$drops[] = [Item::STRING, 0, 1];

		return $drops;
	}
	
	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$this->recalculateBoundingBox();
		}
		return false;
	}
	
    /**
     * Test if tripwire is currently activated
     *
     * @return true if activated, false if not
     */
    public function isActivated() {
        return ($this->getDamage() & 0x04) != 0;
    }
    
    /**
     * Set tripwire activated state
     *
     * @param $act - true if activated, false if not
     */
    public function setActivated($act) {
        $dat = $this->getDamage() & (0x08 | 0x03);
        if ($act) {
            $dat |= 0x04;
        }
        $this->setDamage($dat);
    }    
    
    /**
     * Test if object triggering this tripwire directly
     *
     * @return true if object activating tripwire, false if not
     */
    public function isObjectTriggering() {
        return ($this->getDamage() & 0x01) != 0;
    }

    /**
     * Set object triggering state for this tripwire
     *
     * @param trig - true if object activating tripwire, false if not
     */
    public function setObjectTriggering($trig) {
        $dat = $this->getDamage() & 0x0E;
        if ($trig) {
            $dat |= 0x01;
        }
        $this->setDamage($dat);
    }
    
    public function __toString() : string{
        return $this->getDamage() . ($this->isActivated()?" Activated":"") . ($this->isObjectTriggering()?" Triggered":"");
    }
    
    public function onEntityCollide(Entity $entity){
    	$this->setActivated(true);
		$this->getLevel()->scheduleUpdate($this, 0);
		if($this->getSide(Vector3::SIDE_EAST) instanceof Tripwire) $this->getLevel()->scheduleUpdate($this->getSide(Vector3::SIDE_EAST), 0);
		if($this->getSide(Vector3::SIDE_NORTH) instanceof Tripwire) $this->getLevel()->scheduleUpdate($this->getSide(Vector3::SIDE_NORTH), 0);
		if($this->getSide(Vector3::SIDE_SOUTH) instanceof Tripwire) $this->getLevel()->scheduleUpdate($this->getSide(Vector3::SIDE_SOUTH), 0);
		if($this->getSide(Vector3::SIDE_WEST) instanceof Tripwire) $this->getLevel()->scheduleUpdate($this->getSide(Vector3::SIDE_WEST), 0);
    }
	
	public function isEntityCollided() : bool{
		foreach ($this->getLevel()->getChunk($itementity->x >> 4, $itementity->z >> 4)->getEntities() as $entity){
			if($this->getLevel()->getBlock($entity->getPosition()) === $this)
				return true;
		}
		return false;
	}
}