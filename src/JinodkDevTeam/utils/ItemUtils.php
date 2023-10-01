<?php
declare(strict_types=1);

namespace JinodkDevTeam\utils;

use Exception;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class ItemUtils{

	/**
	 * @param Inventory $inv
	 * @param Item      $other
	 *
	 * @return int
	 *
	 * @description Return count of item in player inventory
	 */
	public static function getItemCount(Inventory $inv, Item $other): int{
		$count = 0;
		foreach ($inv->getContents() as $item){
			if ($item->canStackWith($other)){
				$count += $item->getCount();
			}
		}
		return $count;
	}

	/**
	 * @param Inventory $inventory
	 * @param Item      ...$slots
	 *
	 * @return Item[]
	 *
	 * @description Copy-pasta of PM removeItem() with more strict with item NamedTag check...
	 */
	public static function removeItem(Inventory $inventory, Item ...$slots): array
         {
            /** @var Item[] $itemSlots */
            /** @var Item[] $slots */
           $itemSlots = [];
           foreach ($slots as $slot) {
              if (!$slot->isNull()) {
               $itemSlots[] = clone $slot;
             }
          }

      foreach ($itemSlots as $slot) {
         if ($slot->getCount() <= 0) {
            continue;
        }

        for ($i = 0, $size = $inventory->getSize(); $i < $size; ++$i) {
            $item = $inventory->getItem($i);
            if ($item->isNull()) {
                continue;
            }

            if ($slot->equals($item, true)) {
                $amount = min($item->getCount(), $slot->getCount());
                $slot->setCount($slot->getCount() - $amount);
                $item->setCount($item->getCount() - $amount);
                $inventory->setItem($i, $item);

                if ($slot->getCount() <= 0) {
                    break;
                }
            }
        }
    }

    return $itemSlots;
	 }
	
	public static function toString(Item $item): string{
		$nbt = $item->nbtSerialize();
		return utf8_encode(serialize($nbt));
	}

	public static function fromString(string $string): ?Item{
		try{
			return Item::nbtDeserialize(unserialize(utf8_decode($string)));
		}catch(Exception){
			return null;
		}
	}

	/**
	 * @param Item[] $items
	 *
	 * @return string[]
	 */
	public static function ItemArraytoStringArray(array $items): array{
		$data = [];
		foreach($items as $item){
			array_push($data, self::toString($item));
		}
		return $data;
	}

	/**
	 * @param string[] $data
	 *
	 * @return Item[]
	 */
	public static function StringArrayToItemArray(array $data): array{
		$items = [];
		foreach($data as $d){
			array_push($items, self::fromString($d));
		}
		return $items;
	}
}
