<?php
/**
 * Charactor class
 *
 * PHP version 5
 *
 * @author UDA Tomoyuki <aquamarine0922@gmail.com>
 *
 */

abstract class Charactor {

  protected $i;
  protected $j;

  protected $muki;

  protected $isFirst;

  protected $prevI;
  protected $prevJ;

  protected $gps;
  
  abstract public function move($stage);
  abstract public function getName();


  public function firstMove($stage) {
    if ($this->isFirst) {

      if ($stage->canMoveUnder($this) ) {
	$this->savePrevPosition();
	$this->i = $this->i+1;
	$this->muki = "j"; //下
      } elseif($stage->canMoveLeft($this) ) {
	$this->savePrevPosition();    
	$this->j = $this->j-1;
	$this->muki = "h"; //左
      } elseif($stage->canMoveTop($this) ) {
	$this->savePrevPosition();    
	$this->i = $this->i-1;
	$this->muki = "k"; //上
      } elseif($stage->canMoveRight($this) ) {
	$this->savePrevPosition();    
	$this->j = $this->j+1;
	$this->muki = "l"; //右
      }
      
    }
  }
  
  
  public function setGPS($gps) {
    $this->gps = $gps;
  }

  function __construct($i, $j) {
    $this->i = $i;
    $this->j = $j;
  }

  protected function isPrevLocation($i,$j) {
    if ( ($i == $this->prevI) && ($j == $this->prevJ) ) {
      return true;
    } else {
      return false;
    }
  }

  protected function showStatus() {
    printf("%s : %s(%s) : %s(%s) [%s]\n",$this->getName(), $this->i, $this->prevI, $this->j, $this->prevJ, $this->muki);
  }
  
  public function getI() {
    return $this->i;
  }
  public function getJ() {
    return $this->j;
  }

  public function getPrevI() {
    return $this->prevI;
  }

  public function getPrevJ() {
    return $this->prevJ;
  }

  public function getMuki() {
    return $this->muki;
  }

  protected function savePrevPosition() {
    $this->prevI = $this->i;
    $this->prevJ = $this->j;
  }
  
  public static function factory($char,$i,$j) {

    $charactor="";
    switch($char) {
    case "V":
      $charactor = new Tyrant_T002($i, $j);
      printf("charactor V loaded.\n");
      break;
    case "H":
      $charactor = new Tyrant_T103($i, $j);
      printf("charactor H loaded.\n");
      break;
    case "L":
      $charactor = new CharactorL($i, $j);
      printf("charactor L loaded.\n");
      break;
    case "R":
      $charactor = new CharactorR($i, $j);
      printf("charactor R loaded.\n");
      break;
    case "J":
      $charactor = new CharactorJ($i, $j);
      printf("charactor R loaded.\n");
      break;
    case "@":
      $charactor = new PacMan($i, $j);
      printf("charactor PacMan loaded.\n");
      break;
    }
    return $charactor;
  }


  protected function moveForward() {

    switch($this->muki) {
    case "k":
      $this->i = $this->i - 1;
      $this->muki ="k";
      break;
    case "j":
      $this->i = $this->i + 1;
      $this->muki = "j";
      break;
    case "l":
      $this->j = $this->j + 1;
      $this->muki = "l";
      break;
      case "h":
	$this->j = $this->j - 1;
	$this->muki = "h";
	break;
    }
    
  }

  protected function searchPlayerI() {
    return $this->gps->getPlayerLocationI();
  }

  protected function searchPlayerJ() {
    return $this->gps->getPlayerLocationJ();
  }
  
  protected function moveLeft() {
    
    switch($this->muki) {
    case "k":
      $this->j = $this->j-1;
      $this->muki = "h";
      break;
    case "j":
      $this->j = $this->j+1;
      $this->muki = "l";
      break;
    case "l":
      $this->i = $this->i-1;
      $this->muki = "k";
      break;
    case "h":
      $this->i = $this->i+1;
      $this->muki = "j";
      break;
    }

  }

  protected function moveRight() {

    switch($this->muki) {
    case "k":
      $this->j = $this->j + 1;
      $this->muki ="l";
      break;
    case "j":
      $this->j = $this->j - 1;
      $this->muki = "h";
      break;
    case "l":
      $this->i = $this->i + 1;
      $this->muki = "j";
      break;
    case "h":
      $this->i = $this->i - 1;
      $this->muki = "k";
      break;
    }

  }
  

}


class CharactorJ extends Charactor {

  private $turned;
  
  function __construct($i, $j) {
    parent::__construct($i, $j);
    $this->muki="j";
    $this->turned = 0;
  }



  protected function moveR($stage) {

    $this->savePrevPosition();

    if($stage->canMoveRightWrapper($this)) {
      $this->moveRight();
    } elseif($stage->canMoveForward($this)) {
      $this->moveForward();
    } elseif($stage->canMoveLeftWrapper($this)) {
      $this->moveLeft();
    }
    
  }

  protected function moveL($stage) {

    $this->savePrevPosition();

    if($stage->canMoveLeftWrapper($this)) {
      $this->moveLeft();
    } elseif($stage->canMoveForward($this)) {
      $this->moveForward();
    } elseif($stage->canMoveRightWrapper($this)) {
      $this->moveRight();
    }
    
  }


  private function isInterSection($stage) {
    
    $interSection =0;
    if ($stage->canMoveLeft($this)) {
      $interSection++;
    }
    
    if ($stage->canMoveTop($this)) {
      $interSection++;
    }
    
    if ($stage->canMoveRight($this)) {
      $interSection++;
    }
    
    if($stage->canMoveUnder($this)) {
      $interSection++;
    }

    return $interSection;
    
  }

  
  public function move($stage) {

    if ($this->isFirst) {
      $this->firstMove($stage);
      $this->isFirst = false;
    } else {

      if ($this->isInterSection($stage)>2) {
	$this->turned++;
      }
      if(($this->turned % 2) == 0) {
	$this->moveR($stage);
      } else {
	$this->moveL($stage);

      }
      


    }
    
    $this->showStatus();
    
  }

  public function getName() {
    print "J";
  }
  
}



class CharactorR extends Charactor {

  function __construct($i, $j) {
    parent::__construct($i, $j);
    $this->muki="j";
    $this->isFirst=true;
  }

  public function move($stage) {
    
    $this->savePrevPosition();
    if ($this->isFirst) {
      $this->firstMove($stage);
      $this->isFirst=false;
    } else {
      if($stage->canMoveRightWrapper($this)) {
	$this->moveRight();
      } elseif($stage->canMoveForward($this)) {
	$this->moveForward();
      } elseif($stage->canMoveLeftWrapper($this)) {
	$this->moveLeft();
      }
    }
    
    $this->showStatus();
    
  }

  public function getName() {
    print "R";
  }
  
}


class CharactorL extends Charactor {

  function __construct($i, $j) {
    parent::__construct($i, $j);
    $this->muki="j";
    $this->isFirst = true;
  }

  public function move($stage) {
    
    $this->savePrevPosition();

    if ($this->isFirst) {
      $this->firstMove($stage);
      $this->isFirst = false;
    } else {

      if($stage->canMoveLeftWrapper($this)) {
	$this->moveLeft();
      } elseif($stage->canMoveForward($this)) {
	$this->moveForward();
      } elseif($stage->canMoveRightWrapper($this)) {
	$this->moveRight();
      }
      
    }
    
    $this->showStatus();
    
  }

  public function getName() {
    print "L";
  }
  
}

class Tyrant_T002 extends Charactor{

  function __construct($i, $j) {
    parent::__construct($i, $j);
    $this->savePrevPosition();
  }

  public function move ($stage) {
    
    $isPlayerSearch = false;
    if ( ($this->i > $this->gps->getPlayerLocationI())
	&& ($stage->canMoveTop($this))	&& (!$this->isPrevLocation($this->i-1,$this->j)) ) {
      $this->savePrevPosition();    
      $this->i = $this->i-1;
      $this->muki = "j";
      $isPlayerSearch = true;
    } elseif (($this->gps->getPlayerLocationI() > $this->i)
	      && ($stage->canMoveUnder($this)) && (!$this->isPrevLocation($this->i+1,$this->j)) ) {
      $this->savePrevPosition();    
      $this->i = $this->i+1;
      $this->muki ="k";
      $isPlayerSearch = true;
    } elseif( ($this->j > $this->gps->getPlayerLocationJ())
	      && ($stage->canMoveLeft($this)) && (!$this->isPrevLocation($this->i,$this->j-1)) ) {
      $this->savePrevPosition();    
      $this->j = $this->j - 1;
      $this->muki = "h";
      $isPlayerSearch = true;
    } elseif( ($this->gps->getPlayerLocationJ() > $this->j)
	      && $stage->canMoveRight($this) && (!$this->isPrevLocation($this->i,$this->j+1) )) {
      $this->savePrevPosition();    
      $this->j = $this->j + 1;
      $this->muki = "l";
      $isPlayerSearch = true;
    }elseif ($stage->canMoveUnder($this) && (!$this->isPrevLocation($this->i+1,$this->j)) ) {
      $this->savePrevPosition();
      $this->i = $this->i+1;
      $this->muki = "j"; //下
    } elseif($stage->canMoveLeft($this) && (!$this->isPrevLocation($this->i,$this->j-1)) ) {
      $this->savePrevPosition();    
      $this->j = $this->j-1;
      $this->muki = "h"; //左
    } elseif($stage->canMoveTop($this) && (!$this->isPrevLocation($this->i-1,$this->j)) ) {
      $this->savePrevPosition();    
      $this->i = $this->i-1;
      $this->muki = "k"; //上
    } elseif($stage->canMoveRight($this) && (!$this->isPrevLocation($this->i,$this->j+1)) ) {
      $this->savePrevPosition();    
      $this->j = $this->j+1;
      $this->muki = "l"; //右
    }
    
    $this->showStatus();
  }

  public function getName() {
    print "V";
  }
  
}


class Tyrant_T103 extends Charactor{

  function __construct($i, $j) {
    parent::__construct($i, $j);
    $this->savePrevPosition();
  }

  
  public function move ($stage) {
    
    $isPlayerSearch = false;
    if( ($this->j > $this->gps->getPlayerLocationJ())
	&& ($stage->canMoveLeft($this)) && (!$this->isPrevLocation($this->i,$this->j-1)) ) {
      $this->savePrevPosition();
      $this->j = $this->j - 1;
      $this->muki = "h";
      $isPlayerSearch = true;
    }elseif( ($this->gps->getPlayerLocationJ() > $this->j)
	     && $stage->canMoveRight($this) && (!$this->isPrevLocation($this->i,$this->j+1) )) {
      $this->savePrevPosition();
      $this->j = $this->j + 1;
      $this->muki = "l";
      $isPlayerSearch = true;
    }elseif (($this->i > $this->gps->getPlayerLocationI())
	     && ($stage->canMoveTop($this)) && (!$this->isPrevLocation($this->i-1,$this->j)) ) {
      $this->savePrevPosition();
      $this->i = $this->i-1;
      $this->muki = "j";
      $isPlayerSearch = true;
    }elseif (($this->gps->getPlayerLocationI() > $this->i)
	     && ($stage->canMoveUnder($this)) && (!$this->isPrevLocation($this->i+1,$this->j)) ) {
      $this->savePrevPosition();
      $this->i = $this->i+1;
      $this->muki ="k";
      $isPlayerSearch = true;
    }elseif ($stage->canMoveUnder($this) && (!$this->isPrevLocation($this->i+1,$this->j)) ) {
      $this->savePrevPosition();
      $this->i = $this->i+1;
      $this->muki = "j"; //下
    }elseif($stage->canMoveLeft($this) && (!$this->isPrevLocation($this->i,$this->j-1)) ) {
      $this->savePrevPosition();
      $this->j = $this->j-1;
      $this->muki = "h"; //左
    }elseif($stage->canMoveTop($this) && (!$this->isPrevLocation($this->i-1,$this->j)) ) {
      $this->savePrevPosition();
      $this->i = $this->i-1;
      $this->muki = "k"; //上
    }elseif($stage->canMoveRight($this) && (!$this->isPrevLocation($this->i,$this->j+1)) ) {
      $this->savePrevPosition();
      $this->j = $this->j+1;
      $this->muki = "l"; //右
    }
    
    $this->showStatus();
  }

  public function getName() {
    print "H";
  }
  
}


class GPS {
  
  private $player;
  private $i;
  private $j;

  function __construct($player) {
    $this->i = $player->getI();
    $this->j  = $player->getJ();
    $this->player = $player;
  }

  public function getPlayerLocationI() {
    return $this->player->getI();
  }
  
  public function getPlayerLocationJ() {
    return $this->player->getJ();
  }

}

class PacMan extends Charactor {

  private $eatDot;
  private $cmds;
  
  function __construct($i, $j) {
    parent::__construct($i, $j);
    $eatDot = 0;
    $cmds = array();
  }

  public function move($stage) {

    $isMove = false;
    while(!$isMove) {
      printf("Enter the key[hjkl]: ");
      fscanf(STDIN,"%s",$cmd);
      switch($cmd) {
      case "h":
	if($stage->canMoveLeft($this)) {
	  $this->j = $this->j-1;
	  $this->muki = "h"; //左
	  $isMove = true;
	}
	break;
      case "k":
	if ($stage->canMoveTop($this)) {
	  $this->i = $this->i-1;
	  $this->muki = "k"; //上
	  $isMove = true;
	}
	break;
      case "j":
	if ($stage->canMoveUnder($this)) {
	  $this->i = $this->i+1;
	  $this->muki = "j"; //下
	  $isMove = true;
	}
	
	break;
      case "l":
	if($stage->canMoveRight($this)) {
	  $this->j = $this->j+1;
	  $this->muki = "l"; //右
	  $isMove = true;
	}
	break;
      case ".":
	$isMove = true;
	break;
      }

    }
    $this->cmds[] = $cmd;
    $this->eat($stage);
    $this->showStatus();
    $this->showCmds();
  }

  private function showCmds() {
    print "your command are: ";
    foreach($this->cmds as $cmd) {
      print $cmd;
    }
    print "\n";

    
  }

  
  public function getName() {
    return "@";
  }

  protected function eat($stage) {
    
    if ($stage->getPlaceValue($this->i, $this->j) ==".") {
      $this->eatDot++;
      $stage->deleteDotValue($this->i, $this->j);
    }

  }


}



?>