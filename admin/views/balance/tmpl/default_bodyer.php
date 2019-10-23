<?php
/**
 * @package    CurlingEvent
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$levels=[null,null,null,null,null,null];

foreach ($this->items[1] as $i => $item){
	if($item->doshow==1){
		if($item->type==11){
			//ausgabe Konto
			$this->writeRecord(-1, $item, []);
		}else {
			for($i=5; $i>=$item->level; $i--){
				if($levels[$i]!=null){
					$this->writeRecord(-1, $levels[$i], ['highlight'=>'h'. ($levels[$i]->level+4), 'sum'=>true]);
					$levels[$i]=null;
				}
			}
			$levels[$item->level]=$item;
			//Zwischentitel
			$this->writeRecord(-1, $item, ['highlight'=>'h'. ($item->level+4), 'novalue'=>$item->type!=12]);
		}
	}
}
//die letzten Zwischentotale ausgeben
for($i=5; $i>=0; $i--){
	if($levels[$i]!=null && $levels[$i]->type!=12){
		$this->writeRecord(-1, $levels[$i], ['highlight'=>'h'. ($levels[$i]->level+4), 'sum'=>true]);
		$levels[$i]=null;
	}
}

?>