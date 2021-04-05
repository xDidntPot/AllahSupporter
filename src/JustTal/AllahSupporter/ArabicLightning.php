<?php

declare(strict_types=1);

namespace JustTal\AllahSupporter;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

class ArabicLightning extends Entity
{
    const NETWORK_ID = EntityIds::LIGHTNING_BOLT;
    /** @var float */
    public $width = 0.3;
    /** @var float */
    public $length = 0.9;
    /** @var float */
    public $height = 1.8;
    /** @var int */
    protected $age = 0;

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->closed) {
            return false;
        }
        $this->age += $tickDiff;
        $hasUpdate = parent::entityBaseTick($tickDiff);
        foreach ($this->getLevel()->getNearbyEntities($this->getBoundingBox()->expandedCopy(4, 3, 4), $this) as $entity) {
            if ($entity instanceof Living && $entity->isAlive()) {
                $owner = $this->getOwningEntity();
                if (!$owner instanceof Player) {
                    $ev = new EntityCombustByEntityEvent($this, $entity, mt_rand(3, 8));
                    $ev->call();
                    $entity->setOnFire($ev->getDuration());
                }
                $ev = new EntityDamageByEntityEvent($this, $entity, EntityDamageEvent::CAUSE_CUSTOM, 6969);
                $ev->call();
                $entity->attack($ev);
            }
        }
        if ($this->age > 20) {
            $this->flagForDespawn();
        }
        return $hasUpdate;
    }
}