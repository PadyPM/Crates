<?php

/*
* PocketMine-MP Crates Plugin
* Version 1.0
* It is forbidden to sell and share without permission
* Coding PadyPM
* API: 3.0.0 - 4.0.0+dev
* Plugin Language: Turkish
*/

namespace Pady;

use pocketmine\plugin\PluginBase;
use pocketmine\level\Level;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\{Player, Server};
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\BlazeShootSound;

class Kasa extends PluginBase implements Listener{
	
	public $aktif = array();
	public $cfg;
	
	public function onEnable(): void{
		$this->getLogger()->info("\n§8» §aKasaPM plugin for Pocketmine.\n§8» §6Coding By PadyPM");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getScheduler()->scheduleRepeatingTask(new Particle($this), 20);
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		@mkdir($this->getDataFolder());
		if(!file_exists($this->getDataFolder()."itemler.yml")){
			$this->saveResource("itemler.yml");
		}
	}
	
	public function onCommand(CommandSender $cs, Command $kmt, string $label, array $args): bool{
		if($kmt->getName() == "kasa"){
			if($cs->isOp()){
				if(isset($args[0])){
					if($args[0] == "siradan" || $args[0] == "destansi" || $args[0] == "efsanevi"){
						$this->aktif[$cs->getName()] = $args[0];
						$cs->sendMessage("§8» §aKasayı ayarlamak için bir §6Tuzaklı Sandığa §atıkla.");
					}else{
						$cs->sendMessage("§8» §6$args[0] §cisimli bir kasa türü bulunamadı!");
					}
				}else{
					$cs->sendMessage("§8» §a/kasa §e<§6siradan§e|§6destansi§e|§6efsanevi§e>");
				}
			}
		}
		if($kmt->getName() == "anahtar"){
			if($cs->isOp()){
				if(isset($args[0])){
					if($args[0] == "siradan"){
						if(isset($args[1])){
							$oyuncu = $this->getServer()->getPlayer($args[1]);
							if($oyuncu instanceof Player){
								$oyuncu->sendMessage("§8» §7Sıradan §aAnahtar hesabına eklendi");
								$cs->sendMessage("§8» §6" . $oyuncu . " §aisimli oyuncuya anahtar verdin.");
								$oyuncu->getInventory()->addItem(Item::get(377,0,1)->setCustomName("§7Sıradan §aAnahtar"));
							}
						}
					}elseif($args[0] == "destansi"){
						if(isset($args[1])){
							$oyuncu = $this->getServer()->getPlayer($args[1]);
							if($oyuncu instanceof Player){
								$oyuncu->sendMessage("§8» §eDestansı §aAnahtar hesabına eklendi");
								$cs->sendMessage("§8» §6" . $oyuncu . " §aisimli oyuncuya anahtar verdin.");
								$oyuncu->getInventory()->addItem(Item::get(399,0,1)->setCustomName("§eDestansı §aAnahtar"));
							}
						}
					}elseif($args[0] == "efsanevi"){
						if(isset($args[0])){
							$oyuncu = $this->getServer()->getPlayer($args[1]);
							if($oyuncu instanceof Player){
								$oyuncu->sendMessage("§8» §bEfsanevi §aAnahtar hesabına eklendi");
								$cs->sendMessage("§8» §6" . $oyuncu . " §aisimli oyuncuya anahtar verdin.");
								$oyuncu->getInventory()->addItem(Item::get(421,0,1)->setCustomName("§bEfsanevi §aAnahtar"));
							}
						}else{
							$cs->sendMessage("§b/anahtar <siradan|destansi|efsanevi>");
						}
					}
				}
			}
		}
		return true;
	}
	
	public function kasaTiklama(PlayerInteractEvent $e){
		
		$o = $e->getPlayer();
		$b = $e->getBlock();
		$this->cfg->reload();
		$itemler = new Config($this->getDataFolder()."itemler.yml", Config::YAML);
		if(!empty($this->aktif[$o->getName()])){
			if(!($o->getLevel() == $o->getServer()->getDefaultLevel())){ return; }
			if($b->getId() == 146){
				$tur = $this->aktif[$o->getName()];
				$this->cfg->set($tur, array($b->x, $b->y, $b->z));
				$this->cfg->save();
				$o->sendMessage("§8» §6" . ucfirst($tur) . " §asandığı başarı ile güncellendi.");
				unset($this->aktif[$o->getName()]);
				$e->setCancelled();
			}else{
				$o->sendMessage("§8» §cTuzaklı sandığa dokunmalısın!");
			}
		}else{
			if(!($o->getLevel() == $o->getServer()->getDefaultLevel())){ return; }
			if($b->getId() == 146){
				if($b->getX() == $this->cfg->get("siradan")[0] && $b->getY() == $this->cfg->get("siradan")[1] && $b->getZ() == $this->cfg->get("siradan")[2]){
					if($o->getInventory()->getItemInHand()->getId() == 377){
						if($o->getInventory()->getItemInHand()->getCustomName() == "§7Sıradan §aAnahtar"){
							$o->getLevel()->addSound(new BlazeShootSound($o));
							$brc = $itemler->get("Siradan"); $bri = array_rand($brc);
							$brr = explode(",", $brc[$bri]);
							$item = Item::get($brr[0], $brr[1], $brr[2]);
							$o->getInventory()->addItem($item);
							$o->sendPopup("§8» §aKasadan §6".$item->getName()."§a çıkardın!\n".str_repeat(" ", 6)."§8» §7Sıradan §akasa açtın");
						$ei = $o->getInventory()->getItemInHand();
						$ei->setCount(1);
						$o->getInventory()->removeItem($ei);
						$e->setCancelled(true);
					}else{
						$o->getLevel()->addSound(new AnvilFallSound($o));
						$o->sendPopUp("§8» §cLütfen kasayı §6Sıradan§c anahtar kullanarak açın!");
						$e->setCancelled(true);
					}
				}else{
					$o->getLevel()->addSound(new AnvilFallSound($o));
					$o->sendPopUp("§8» §cLütfen kasayı §6Sıradan§c anahtar kullanarak açın!");
						$e->setCancelled(true);
				}
			}elseif($b->getX() == $this->cfg->get("destansi")[0] && $b->getY() == $this->cfg->get("destansi")[1] && $b->getZ() == $this->cfg->get("destansi")[2]){
				if($o->getInventory()->getItemInHand()->getId() == 399){
						if($o->getInventory()->getItemInHand()->getCustomName() == "§eDestansı §aAnahtar"){
							$o->getLevel()->addSound(new BlazeShootSound($o));
							$dec = $itemler->get("Destansi");
							$dee = explode(",", $dec[array_rand($dec)]);
							$item = Item::get($dee[0], $dee[1], $dee[2]);
							$o->getInventory()->addItem($item);
							$o->sendPopup("§8» §aKasadan §6".$item->getName()."§a çıkardın!\n".str_repeat(" ", 6)."§8» §eDestansı§a kasa açtın");
					$ei = $o->getInventory()->getItemInHand();
						$ei->setCount(1);
						$o->getInventory()->removeItem($ei);
						$e->setCancelled(true);
				}else{
					$o->getLevel()->addSound(new AnvilFallSound($o));
					$o->sendPopUp("§8» §cLütfen kasayı §6Destansı§c anahtar kullanarak açın!");
						$e->setCancelled(true);
				}
			}else{
				$o->getLevel()->addSound(new AnvilFallSound($o));
				$o->sendPopUp("§8» §cLütfen kasayı §6Destansı§c anahtar kullanarak açın!");
						$e->setCancelled(true);
			}
		}elseif($b->getX() == $this->cfg->get("efsanevi")[0] && $b->getY() == $this->cfg->get("efsanevi")[1] && $b->getZ() == $this->cfg->get("efsanevi")[2]){
			if($o->getInventory()->getItemInHand()->getId() == 421){
						if($o->getInventory()->getItemInHand()->getCustomName() == "§bEfsanevi §aAnahtar"){
							$o->getLevel()->addSound(new BlazeShootSound($o));
							$elc = $itemler->get("Efsanevi");
							$ell = explode(",", $elc[array_rand($elc)]);
							$item = Item::get($ell[0], $ell[1], $ell[2]);
							$o->getInventory()->addItem($item);
							$o->sendPopup("§8» §aKasadan §6".$item->getName()."§a çıkardın!\n".str_repeat(" ", 6)."§8» §bEfsanevi §akasa açtın");
				$ei = $o->getInventory()->getItemInHand();
				$ei->setCount(1);
				$o->getInventory()->removeItem($ei);
				$e->setCancelled(true);
			}else{
				$o->getLevel()->addSound(new AnvilFallSound($o));
				$o->sendPopUp("§8» §cLütfen kasayı §bEfsanevi§c anahtar kullanarak açın!");
						$e->setCancelled(true);
			}
		}else{
			$o->getLevel()->addSound(new AnvilFallSound($o));
			$o->sendPopUp("§8» §cLütfen kasayı §bEfsanevi§c anahtar kullanarak açın!");
						$e->setCancelled(true);
					}
				}
 		}
 	}
 }
}
