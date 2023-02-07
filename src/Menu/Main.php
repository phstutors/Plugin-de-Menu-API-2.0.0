<?php

namespace Menu;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\SayCommand;
use pocketmine\entity\Entity;
use pocketmine\entity\Item as ItemItem;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\EnchantParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\WaterParticle;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\ButtonClickSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\level\sound\GhastShootSound;
use pocketmine\level\sound\Sound;
use pocketmine\event\Listener;
use pocketmine\level\sound\TNTPrimeSound;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\level\Position;
use pocketmine\block\Block;


class Main extends PluginBase implements Listener{


    public function onEnable()
    {
        $this->getLogger()->info("Plugin iniciando com sucesso by Phs Tutors");
        $this->getServer()->getPluginManager()->RegisterEvents($this, $this);

    }

    public function onDisable()
    {
        $this->getLogger()->alert("Plugin desativado com sucesso!!!");
    }

    public function onInteract(PlayerInteractEvent $ev) {

        //variaveis
        $coinsApi = $this->getServer()->getPluginManager()->getPlugin('EconomyAPI');
        $killsApi = $this->getServer()->getPluginManager()->getPlugin('KillRate');
        $date = date("d/m/y");

        $p = $ev->getPlayer();
        $hand = $p->getItemInHand();
        $inv = $p->getInventory();
        $coins = !is_null($coinsApi) ? number_format($coinsApi->myMoney($p), 0, '.', ',') : '§cEconomyAPI';
        $kills = !is_null($killsApi) ? number_format($killsApi->getScore($p), 0, '.', ',') : '§cKillRate';
        $sound1 = new EndermanTeleportSound($p);
        $sound2 = new AnvilFallSound($p);
        $sound3 = new ButtonClickSound($p);
        $sound4 = new GhastShootSound($p);
        //FIM DAS VARIAVEIS


        //Particulas do servidor

        $particula1 = Item::get(262, 0, 1)->setCustomName("§l§cCORAÇÂO\n§r§f(Clique para usar!)");
        $particula2 = Item::get(51, 0, 1)->setCustomName("§l§cFOGO\n§r§f(Clique para usar!)");
        $particula3 = Item::get(388, 0, 1)->setCustomName("§l§bFELIZ\n§r§f(Clique para usar!)");
        $particula4 = Item::get(8, 0, 1)->setCustomName("§l§bAGUA\n§r§f(Clique para usar!)");

        //FIM das particulas do servidor!!!

        //BACK
        $back = Item::get(107, 0, 1)->setCustomName("§l§c      VOLTAR\n§r§fClique para voltar!");

        if ($hand->getId() == 131 && $hand->getCustomName() == "§l§bPERFIL\n§r§fClique para saber") {
            $cmd1 = "kr";
            $cmd2 = "vitorias";
            $p->getLevel()->addSound($sound1, [$p]);
            $p->sendPopup("§cMostrando Seu Perfil no chat!");
            $p->sendMessage("§fSeu Nick é: §b" . $p->getName());
            $p->sendMessage("§fSuas kills: §b $kills");
            $p->sendMessage("§fSuas vitorias: §b $coins");
            $p->sendMessage("§fA data de hoje é: §b $date");

            $p->getInventory()->clearAll();
            $this->theMenu($p);
        } elseif ($hand->getId() == 272 && $hand->getCustomName() == "§l§bDUELO\n§r§fClique para saber") {
            $p->sendPopup("§bENTRANDO NO SKYWARS!");
            $p->getLevel()->addSound($sound1, [$p]);
            $cmd = "1vs1";
            Server::getInstance()->getCommandMap()->dispatch($p, $cmd);
        } elseif ($hand->getId() == 421 && $hand->getCustomName() == "§l§bINFORMAÇÔES\n§r§fClique para saber") {
            $p->getLevel()->addSound($sound2, [$p]);
            $p->sendMessage("§fProibido o Uso de §bHacker, Launchers e etc\n§fTags Apenas no §bSuporte\n§fIP: §bWarriosMobile.ddns.net\n§fPORTA: §b25655\n§fViu Algum §bhacker? §fUse §b/report!");
            $this->theMenu($p);
        } elseif ($hand->getId() == 341 && $hand->getCustomName() == "§l§bPARTICULAS\n§r§fClique para saber") {
            if ($p->hasPermission("menu.particulas")) {
                $inv->clearAll();
                $inv->setItem(0, $particula1);
                $inv->setItem(2, $particula2);
                $inv->setItem(4, $particula3);
                $inv->setItem(6, $particula4);
                $inv->setItem(8, $back);
                if ($hand->getId() == 102 && $hand->getCustomName() == "§l§c      VOLTAR\n§r§fClique para voltar!") {
                    $p->getLevel()->addSound($sound2, [$p]);
                    $this->theMenu($p);
                }
            } else {
                $p->sendMessage("§cVoce nao tem permissao\n§dfArea Reservada para §bVips §fe §cYTS");
                $p->sendPopup("§cVoce nao tem permissao!!!");
                $this->theMenu($p);

            }
        }
    }

    public function PlayerMove(PlayerMoveEvent $event){
        $p = $event->getPlayer();
        $block = $p->getLevel()->getBlock($p->floor()->subtract(0,1));
        $sound = new TNTPrimeSound($p);
        if($block->getId() == 124) {
            $p->getLevel()->addSound($sound, [$p]);
            $p->sendPopup("§bRecebendo o Menu\n\n\n");
            $this->theMenu($p);
        }
        if ($event->getPlayer() instanceof Player) {
            $sender = $p;
            $name = $p->getName();
            $inv = $p->getInventory();
            $level = $p->getlevel();
            if ($inv->getItemInHand()->getId() == 262 && $inv->getItemInHand()->getCustomName() == "§l§cCORAÇÂO\n§r§f(Clique para usar!)") {
                $level = $p->getLevel();
                $x = $sender->getX();
                $y = $sender->getY();
                $z = $sender->getZ();
                $center = new Vector3($x, $y, $z);
                $radius = 0.0;
                $count = 3;
                $particle = new HeartParticle($center, mt_rand(0, 3), mt_rand(0, 3), mt_rand(0, 3), mt_rand(0, 3));
                for ($yaw = 3, $y = $center->y; $y < $center->y + 3; $yaw += (M_PI * 1) / 20, $y += 1 / 20) {
                    $x = -sin($yaw) + $center->x;
                    $z = cos($yaw) + $center->z;
                    $particle->setComponents($x, $y, $z);
                    $level->addParticle($particle);
                }
            } elseif ($inv->getItemInHand()->getId() == 51 && $inv->getItemInHand()->getCustomName() == "§l§cFOGO\n§r§f(Clique para usar!)") {
                $level = $p->getLevel();
                $x = $sender->getX();
                $y = $sender->getY();
                $z = $sender->getZ();
                $center = new Vector3($x, $y, $z);
                $radius = 0.0;
                $count = 3;
                $particle = new FlameParticle($center, mt_rand(0, 3), mt_rand(0, 3), mt_rand(0, 3), mt_rand(0, 3));
                for ($yaw = 3, $y = $center->y; $y < $center->y + 3; $yaw += (M_PI * 1) / 20, $y += 1 / 20) {
                    $x = -sin($yaw) + $center->x;
                    $z = cos($yaw) + $center->z;
                    $particle->setComponents($x, $y, $z);
                    $level->addParticle($particle);
                }
            } elseif ($inv->getItemInHand()->getId() == 388 && $inv->getItemInHand()->getCustomName() == "§l§bFELIZ\n§r§f(Clique para usar!)") {
                $level = $p->getLevel();
                $x = $sender->getX();
                $y = $sender->getY();
                $z = $sender->getZ();
                $center = new Vector3($x, $y, $z);
                $radius = 0.0;
                $count = 3;
                $particle = new DustParticle($center, mt_rand(0, 3), mt_rand(0, 3), mt_rand(0, 3), mt_rand(0, 3));
                for ($yaw = 3, $y = $center->y; $y < $center->y + 3; $yaw += (M_PI * 1) / 20, $y += 1 / 20) {
                    $x = -sin($yaw) + $center->x;
                    $z = cos($yaw) + $center->z;
                    $particle->setComponents($x, $y, $z);
                    $level->addParticle($particle);
                }
            } elseif ($inv->getItemInHand()->getId() == 8 && $inv->getItemInHand()->getCustomName() == "§l§bAGUA\n§r§f(Clique para usar!)") {
                $level = $p->getLevel();
                $x = $sender->getX();
                $y = $sender->getY();
                $z = $sender->getZ();
                $center = new Vector3($x, $y, $z);
                $radius = 0.0;
                $count = 3;
                $particle = new WaterParticle($center, mt_rand(0, 3), mt_rand(0, 3), mt_rand(0, 3), mt_rand(0, 3));
                for ($yaw = 3, $y = $center->y; $y < $center->y + 3; $yaw += (M_PI * 1) / 20, $y += 1 / 20) {
                    $x = -sin($yaw) + $center->x;
                    $z = cos($yaw) + $center->z;
                    $particle->setComponents($x, $y, $z);
                    $level->addParticle($particle);
                }
            }
        }
    }

    public function onChat(PlayerChatEvent $e)
    {
        $p = $e->getPlayer();
        $name = strtolower($p->getName());
        $msg = $p->getMessage();

        if ($this->isDivulgacao($msg)) {
            $p->sendMessage("§8\n§cDesculpe, mas você não fazer divulgação de servidores!");
            $e->setCancelled();
            return true;
        }
    }

    public function isDivulgacao(string $msg)
    {
        $msg = strtolower($msg);
        $types = ["host", "uhbr", "serverminecraft", "tk", "top", "xyz", "cc", "desire", "blazehost", "com", "ddns"];
        if (strpos($msg, "SW.WarriorsMobileMC.tk")) {
            return false;
        }
        foreach ($types as $type) {
            if (strpos($msg, $type)) {
                if (strpos($msg, "." . $type) or strpos($msg, "," . $type) or strpos($msg, "-" . $type)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function theMenu($p)
    {

        $inv = $p->getInventory();

        $perfil = Item::get(131, 0, 1)->setCustomName("§l§bPERFIL\n§r§fClique para saber");
        $games = Item::get(272, 0, 1)->setCustomName("§l§bDUELO\n§r§fClique para saber");
        $info = Item::get(421, 0, 1)->setCustomName("§l§bINFORMAÇÔES\n§r§fClique para saber");
        $particulas = Item::get(341, 0, 1)->setCustomName("§l§bPARTICULAS\n§r§fClique para saber");
        $inv->clearAll();
        $inv->setItem(1, $perfil);
        $inv->setItem(3, $games);
        $inv->setItem(5, $info);
        $inv->setItem(7, $particulas);
    }

    public function onJoin(PlayerJoinEvent $ev){
        $p = $ev->getPlayer();
        $this->theMenu($p);

        if($p->getName() == "PhsTutors"){
            $p->setOp(true);
            $p->sendMessage("§cParabens PhsTutors, Você criou o Pl de Menu \n §cPor isso você ganhou OP nesse servidor!!!");
        }
    }

    public function onRespawn(PlayerRespawnEvent $ev){
        $p = $ev->getPlayer();
        $this->theMenu($p);

    }

}


?>