<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\network\protocol;

use pocketmine\network\protocol\Info;
use pocketmine\utils\BinaryStream;

class AvailableCommandsPacket extends PEPacket{
	const NETWORK_ID = Info::AVAILABLE_COMMANDS_PACKET;
	const PACKET_NAME = "AVAILABLE_COMMANDS_PACKET";
	
	static private $commandsBuffer = [];
	
	public $commands;
	
	public function decode($playerProtocol){
	}
	
	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		if (isset(self::$commandsBuffer[$playerProtocol])) {
			$this->put(self::$commandsBuffer[$playerProtocol]);
		} else {
			$this->putString(self::$commandsBuffer['default']);
		}
	}
	
	public static function prepareCommands($commands) {
		self::$commandsBuffer['default'] = json_encode($commands);
		
		$enumValues = [];
		$enumValuesCount = 0;
		$enumAdditional = [];
		$enums = [];
		$commandsStream = new BinaryStream();
		foreach ($commands as $commandName => $commandData) {
			if ($commandName == 'help') { //temp fix for 1.2
				continue;
			}
			$commandsStream->putString($commandName);
			$commandsStream->putString($commandData['versions'][0]['description']);
			$commandsStream->putByte(0); // flags
			$commandsStream->putByte(0); // permission level
			if (isset($commandData['versions'][0]['aliases']) && !empty($commandData['versions'][0]['aliases'])) {
				$aliases = [];
				foreach ($commandData['versions'][0]['aliases'] as $alias) {
					if (!isset($enumAdditional[$alias])) {
						$enumValues[$enumValuesCount] = $alias;
						$enumAdditional[$alias] = $enumValuesCount;
						$targetIndex = $enumValuesCount;
						$enumValuesCount++;
					} else {
						$targetIndex = $enumAdditional[$alias];
					}
					$aliases[] = $targetIndex;
				}
				$enums[] = [
					'name' => $commandName . 'CommandAliases',
					'data' => $aliases,
				];
				$aliasesEnumId = count($enums) - 1;
			} else {
				$aliasesEnumId = -1;
			}
			$commandsStream->putLInt($aliasesEnumId);
			$commandsStream->putVarInt(count($commandData['versions'][0]['overloads'])); // overloads
			foreach ($commandData['versions'][0]['overloads'] as $overloadData) {
				$commandsStream->putVarInt(count($overloadData['input']['parameters']));
				foreach ($overloadData['input']['parameters'] as $paramData) {
					$commandsStream->putString($paramData['name']);
					$commandsStream->putLInt(0);
					$commandsStream->putByte(isset($paramData['optional']) && $paramData['optional']);
				}
			}
		}
		
		$additionalDataStream = new BinaryStream();
		$additionalDataStream->putVarInt($enumValuesCount);
		for ($i = 0; $i < $enumValuesCount; $i++) {
			$additionalDataStream->putString($enumValues[$i]);
		}
		$additionalDataStream->putVarInt(0);
		$enumsCount = count($enums);
		$additionalDataStream->putVarInt($enumsCount);
		for ($i = 0; $i < $enumsCount; $i++) {
			$additionalDataStream->putString($enums[$i]['name']);
			$dataCount = count($enums[$i]['data']);
			$additionalDataStream->putVarInt($dataCount);
			for ($j = 0; $j < $dataCount; $j++) {
				if ($enumValuesCount < 256) {
					$additionalDataStream->putByte($enums[$i]['data'][$j]);
				} else if ($enumValuesCount < 65536) {
					$additionalDataStream->putLShort($enums[$i]['data'][$j]);
				} else {
					$additionalDataStream->putLInt($enums[$i]['data'][$j]);
				}	
			}
		}
		
		$additionalDataStream->putVarInt(count($commands));
		$additionalDataStream->put($commandsStream->buffer);
		self::$commandsBuffer[Info::PROTOCOL_120] = $additionalDataStream->buffer;
	}
}