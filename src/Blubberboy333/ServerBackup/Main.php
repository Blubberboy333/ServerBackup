<?php

namespace Blubberboy333\ServerBackup;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
    public function onEnable(){
		if(!(is_dir($this->getDataFolder()))){
			mkdir($this->getDataFolder());
		}
        if(!(is_dir($this->getDataFolder()."Backups/"))){
            mkdir($this->getDataFolder()."Backups/");
            $this->getLogger()->info(TextFormat::BLUE."Made a directory for backups...");
        }
        $this->getLogger()->info(TextFormat::GREEN."Done!");
		$day = date("l");
		$date = date("j");
		$month = date("F");
		$year = date("Y");
        $this->getLogger()->info(TextFormat::BLUE." Today's date is: ".$day.", ".$month." ".$date.", ".$year);
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool {
        if(strtolower($command->getName()) == "backup"){
            if($sender->hasPermission("backup") || $sender->hasPermission("backup.cmd")){
                $backupDir = $this->getDataFolder()."Backups/".date("M")."-".date("j")."-".date("Y");
                if(!(is_dir($backupDir))){
                    $sender->sendMessage(TextFormat::GREEN."Backing up the server worlds...");
                    mkdir($backupDir);
                    $this->backupServer($backupDir);
                    $sender->sendMessage(TextFormat::GREEN."Done!");
                    if($sender instanceof Player){
                        $this->getLogger()->info(TextFormat::GREEN.$sender->getName()." backed up the server!");
                    }
                    return true;
                }else{
                    $sender->sendMessage(TextFormat::YELLOW."The server has already been backed up today!");
					return true;
				}
            }else{
                $sender->sendMessage(TextFormat::RED."You don't have permission to use that command!");
                return true;
            }
        }
    }
    
    public function backupServer($backupDir){
	foreach(glob($this->getServer()->getDataPath()."worlds/*") as $a){
		$worldName = basename($a);
		mkdir($backupDir."/".$worldName."/");
		mkdir($backupDir."/".$worldName."/region");
		copy($this->getServer()->getDataPath()."worlds/".$worldName."/level.dat", $backupDir."/".$worldName."/level.dat");
		$files = scandir($this->getServer()->getDataPath()."worlds/".$worldName."/region");
		foreach($files as $b){
			$fileName = basename($b);
			if(is_file($this->getServer()->getDataPath()."worlds/".$worldName."/region/".$fileName)){
				$old = $this->getServer()->getDataPath()."worlds/".$worldName."/region/".$fileName;
				$new = $backupDir."/".$worldName."/region/".$fileName;
				copy($old, $new);
			}
		}
	}
    }
}
