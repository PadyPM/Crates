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

use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\scheduler\Task;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

class Particle extends Task{
	
public function __construct($plugin){
		$this->p = $plugin;
	}
	
	public function onRun($tick){
		$this->p->cfg->reload();
		$cfg = $this->p->cfg;
		$level = $this->p->getServer()->getDefaultLevel();
		if($cfg->get("siradan")){
			$konum = $this->randVector($cfg->get("siradan")[0], $cfg->get("siradan")[1], $cfg->get("siradan")[2]);
			$particle = new ExplodeParticle($konum, 205, 127, 50);
			$level->addParticle($particle);
		}
		if($cfg->get("destansi")){
			$konum = $this->randVector($cfg->get("destansi")[0], $cfg->get("destansi")[1], $cfg->get("destansi")[2]);
			$particle = new SmokeParticle($konum, 205, 127, 50);
			$level->addParticle($particle);
		}
		if($cfg->get("efsanevi")){
			$konum = $this->randVector($cfg->get("efsanevi")[0], $cfg->get("efsanevi")[1], $cfg->get("efsanevi")[2]);
			$particle = new HeartParticle($konum);
			$level->addParticle($particle);
		}
	}
	
	public function randVector($x, $y, $z){
   return new Vector3($x+(mt_rand()/mt_getrandmax())*2+-0.5,
   $y+(mt_rand()/mt_getrandmax())*0.5+0.5,
   $z+(mt_rand()/mt_getrandmax())*2+-0.5);
 }
}