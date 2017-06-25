<?php
<<<<<<< HEAD
namespace pocketmine\entity;

use pocketmine\item\Item as ItemItem;
=======

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\entity;

>>>>>>> master
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

class CaveSpider extends Monster{
	const NETWORK_ID = 40;

<<<<<<< HEAD
	public $width = 1.438;
	public $length = 1.188;
	public $height = 0.547;
	
	protected $exp_min = 5;
	protected $exp_max = 5;
	protected $maxHealth = 12;

	public function initEntity(){
		parent::initEntity();
	}

	public function getName(){
		return "Cave Spider";
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->type = self::NETWORK_ID;
		$pk->entityRuntimeId = $this->getId();
=======
	public $width = 1;
	public $length = 1;
	public $height = 0.5;

	public $dropExp = [5, 5];

	public function getName() : string{
		return "Cave Spider";
	}
	
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = CaveSpider::NETWORK_ID;
>>>>>>> master
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}
<<<<<<< HEAD

	public function getDrops(){
		return[
			ItemItem::get(ItemItem::STRING, 0, mt_rand(0, 2)),
			ItemItem::get(ItemItem::SPIDER_EYE, 0, mt_rand(0, 1))
		];
	 }
  	
}
=======
}
>>>>>>> master
