<?php

/**
 *
 * google developer day.
 * devquiz.
 * 
 * pacman.
 *
 * PHP version 5
 *
 * @author UDA Tomoyuki <aquamarine0922@gmail.com>
 *
 * Usage: php pacman.php [turn] [w] [h] [maptext]
 * 
 */

require_once "Charactor.class.php";



//$game = new Game($argv[1], $argv[2], $argv[3], $argv[4]);
$game = new Game(50, 11, 7, "level1.txt");
$game->start();

/**
$player = new PacMan(5,5);
$stage = "";
$player->move($stage);
*/

class Game {

  private $stage;
  private $turn;
  
  function __construct($turn, $w, $h, $mapFile) {
    $this->turn = $turn;
    $this->stage = new Stage($w, $h,$mapFile);
  }

  public function start() {
    $this->stage->start($this->turn);
  }
  
}


class Stage {
  
  private $map;
  private $w;
  private $h;
  private $charactors;
  private $player;
  
  function __construct($w, $h, $mapFile) {
    
    $this->w = $w;
    $this->h = $h;
    $this->charactors = array();
    
    try {
      $fp = fopen($mapFile,"r");
      $i=0;
      $j=0;
      $this->map = array();
      while(!feof($fp)) {
	$char = fgetc($fp);
	if ($char != "\n" && $char) {
	  $charactor = Charactor::factory($char, $i, $j);
	  //	  if (is_object($charactor) && is_a($charactor, "Charactor")  ) {
	  if (is_object($charactor) && !is_a($charactor, "PacMan")  ) {
	    $this->map[$i][$j] = " ";
	    $this->charactors[] = $charactor;
	  }elseif(is_object($charactor) && is_a($charactor, "PacMan")) {
	    $this->map[$i][$j] = " ";
	    $player = $charactor;
	  } else {
	    $this->map[$i][$j] = $char;
	  }

	  if (is_a($charactor, "PacMan")) {
	    $this->player = $charactor;
	  }
	  
	  $j++;
	} elseif ($char == "\n") {
	  $i++;
	  $j=0;
	}
      }

      array_unshift($this->charactors, $player);
    
      fclose($fp);
    } catch (Exception $e) {
      printf("program execute error: %s\n", $e->getMessage());
      exit;
    }
    
  }

  public function viewMap() {
    $i = 0;
    while($this->h > $i) {
      $j = 0;
      while($this->w > $j) {
	$stayCharactor = false;
	$aryPosition = array();
	foreach($this->charactors as $charactor) {
	  if (($charactor->getI() == $i) && ($charactor->getJ()) == $j) {
	    if (!isset($aryPosition[$i][$j])) {
	      print $charactor->getName();
	      $aryPosition[$i][$j] = "*";
	      $stayCharactor = true;
	    }
	  }
	}
	
	if (!$stayCharactor) {
	  print $this->map[$i][$j];
	}
	$j++;
      }
      print "\n";
      $i++;
    }
  }

  public function getPlaceValue($i,$j) {
    return $this->map[$i][$j];
  }

  public function deleteDotValue($i, $j) {
    if ($this->map[$i][$j] == ".") {
      $this->map[$i][$j] = " ";
    }
  }

  public function start($turn) {
    
    printf("game start! sort by\n");
    foreach($this->charactors as $charactor) {
      printf("%s \n", $charactor->getName());
    }
    
    $i=0;
    $gps = new GPS($this->player);
    $this->viewMap();
    while($turn>$i) {
      printf("======== %02d turn Start ========\n",$i+1);
      foreach($this->charactors as $charactor) {
	$charactor->setGPS($gps);
	$charactor->move($this);
      }
      $this->viewMap();
      printf("======== %02d turn end ========\n",$i+1);
      $i++;
    }
  }

  public function canMoveUnder($charactor) {
    
    if ($this->map[$charactor->getI()+1][$charactor->getJ()] != "#") {
      return true;
    } else {
      return false;
    }
    
  }

  public function canMoveTop($charactor) {

    if ($this->map[$charactor->getI()-1][$charactor->getJ()] != "#") {
      return true;
    } else {
      return false;
    }
    
  }

  public function canMoveRight($charactor) {

    if ($this->map[$charactor->getI()][$charactor->getJ()+1] != "#") {
      return true;
    } else {
      return false;
    }
    
  }

  public function canMoveLeft($charactor) {

    if ($this->map[$charactor->getI()][$charactor->getJ()-1] != "#") {
      return true;
    } else {
      return false;
    }
    
  }

  public function canMoveForward($charactor) {

    switch($charactor->getMuki()) {
    case "h":
      return $this->canMoveLeft($charactor);
      break;
    case "k":
      return $this->canMoveTop($charactor);
      break;
    case "j":
      return $this->canMoveUnder($charactor);
      break;
    case "l":
      return $this->canMoveRight($charactor);
      break;
    }
  }

  public  function canMoveLeftWrapper($charactor) {

    switch($charactor->getMuki()) {
    case "h":
      return $this->canMoveUnder($charactor);
      break;
    case "k":
      return $this->canMoveleft($charactor);
      break;
    case "j":
      return $this->canMoveRight($charactor);
      break;
    case "l":
      return $this->canMoveTop($charactor);
      break;
    }
    
    
  }

  
  public function canMoveRightWrapper($charactor) {
    
    switch($charactor->getMuki()) {
    case "h":
      Return $this->canMoveTop($charactor);
      break;
    case "k":
      return $this->canMoveRight($charactor);
      break;
    case "j":
      return $this->canMoveLeft($charactor);
      break;
    case "l":
      return $this->canMoveUnder($charactor);
      break;
    }
    
  }

  
}

?>