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

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\EnderChest as TileEnderChest;
use pocketmine\tile\Tile;

class EnderChest extends Chest{

	protected $id = self::ENDER_CHEST;

	public function getHardness() : float{
		return 22.5;
	}

	public function getBlastResistance() : float{
		return 3000;
	}

	public function getLightLevel() : int{
		return 7;
	}

	public function getName() : string{
		return "Ender Chest";
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3
		];

		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];

		$this->getLevel()->setBlock($blockReplace, $this, true, true);
		Tile::createTile(Tile::ENDER_CHEST, $this->getLevel(), TileEnderChest::createNBT($this, $face, $item, $player));

		return true;
	}

	public function onBreak(Item $item, Player $player = null) : bool{
		return Block::onBreak($item, $player);
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if($player instanceof Player){

			$t = $this->getLevel()->getTile($this);
			$enderChest = null;
			if($t instanceof TileEnderChest){
				$enderChest = $t;
			}else{
				$enderChest = Tile::createTile(Tile::ENDER_CHEST, $this->getLevel(), TileEnderChest::createNBT($this));
			}

			if(!$this->getSide(Vector3::SIDE_UP)->isTransparent()){
				return true;
			}

			$player->getEnderChestInventory()->setHolderPosition($enderChest);
			$player->addWindow($player->getEnderChestInventory());
		}

		return true;
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= TieredTool::TIER_WOODEN){
			return [ItemFactory::get(Item::OBSIDIAN, 0, 8)];
		}

		return [];
	}

	public function getFuelTime() : int{
		return 0;
	}

}