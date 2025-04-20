<?php

declare(strict_types=1);

namespace wavycraft\wavyeconomyscore;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\Server;

use pocketmine\player\Player;

use wavycraft\wavyeconomy\api\WavyEconomyAPI;
use wavycraft\wavyeconomy\event\BalanceChangeEvent;

use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;

class EventListener implements Listener {

    protected function updateTag(Player $player) : void{
        if (class_exists(ScoreHud::class)) {
            $balance = WavyEconomyAPI::getInstance()->getBalance($player->getName());

            $ev = new PlayerTagsUpdateEvent(
                $player,
                [
                    new ScoreTag("wavyeconomy.balance", (string)number_format($balance))
                ]
            );
            $ev->call();
        }
    }

    public function join(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        
        $this->updateTag($player);
    }

    public function balanceChange(BalanceChangeEvent $event) : void{
        $player = $event->getPlayer();

        Server::getInstance()->getPlayerByPrefix(ucfirst($player));

        if ($player instanceof Player) {
            $this->updateTag($player);
        }
    }

    public function onTagResolve(TagsResolveEvent $event) : void{
        $player = $event->getPlayer();
        $tag = $event->getTag();
        $balance = WavyEconomyAPI::getInstance()->getBalance($player->getName());

        match ($tag->getName()) {
            "wavyeconomy.balance" => $tag->setValue((string)number_format($balance)),
            default => null,
        };
    }
}
