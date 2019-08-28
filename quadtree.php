<?php
/*
* Written by Long@Sunteam
*/

class Rect
{
	public $x;
	public $y;
	public $w;
	public $h;

	function __construct($x, $y, $w, $h)
	{
		$this->x = $x;
		$this->y = $y;
		$this->w = $w;
		$this->h = $h;
	}

	function isContain($rect)
	{
		return !(($rect->x + $rect->w) < $this->x
			|| ($rect->y + $rect->h) < $this->y
			|| $rect->x > ($this->x + $this->w)
			|| $rect->y > ($this->y + $this->h));
	}
};

class Quadtree
{
	public $rect;
	function __construct($x, $y, $w, $h, $level, $maxObj, $maxLevel = 4)
	{
		$this->rect = new Rect($x, $y, $w, $h);
		$this->listObjs = [];
		$this->level = $level + 1;
		$this->maxObj = $maxObj;
		$this->node = [];
		$this->maxLevel = $maxLevel;
	}

	function isContain($rect)
	{
		return $this->rect->isContain($rect);
	}

	function split()
	{
		$nw = $this->rect->w / 2;
		$nh = $this->rect->h / 2;

		$this->node[0] = new Quadtree($this->rect->x, $this->rect->y, $nw, $nh, $this->level, $this->maxObj);

		$this->node[1] = new Quadtree($this->rect->x + $nw, $this->rect->y, $nw, $nh, $this->level, $this->maxObj);

		$this->node[2] = new Quadtree($this->rect->x, $this->rect->y + $nh, $nw, $nh, $this->level, $this->maxObj);

		$this->node[3] = new Quadtree($this->rect->x + $nw, $this->rect->y + $nh, $nw, $nh, $this->level, $this->maxObj);
	}

	function insertObj($obj1)
	{
		if (!$this->isContain($obj1)) {
			return false;
		}

		if (count($this->listObjs) <= $this->maxObj - 1) {
			$this->listObjs[] = $obj1;
		} else {
			if ($this->level >= $this->maxLevel) {
				$this->listObjs[] = $obj1;
				return;
			}

			$this->listObjs[] = $obj1;
			if (count($this->node)) {
				for ($j = 0; $j < count($this->node); $j++) {
					if ($this->node[$j]->isContain($obj1)) {
						$this->node[$j]->insertObj($obj1);
					}
				}
			} else {
				$this->split();
				while (count($this->listObjs)) {
					$currObj = array_pop($this->listObjs);
					for ($j = 0; $j < count($this->node); $j++) {
						if ($this->node[$j]->isContain($currObj)) {
							$this->node[$j]->insertObj($currObj);
						}
					}
				}
			}
		}
	}

	function retrieve($rect)
	{
		$list = [];
		if ($this->isContain($rect)) {
			if (count($this->listObjs)) {
				$list = $this->listObjs;
			} else if (count($this->node)) {
				for ($i = 0; $i < count($this->node); $i++) {
					if ($this->node[$i]->isContain($rect)) {
						$list = array_merge($list, $this->node[$i]->listObjs);
					}
				}
			}
		}

		return $list;
	}
}

$q = new Quadtree(0, 0, 10, 10, 0, 2);

//var_dump($q);
$o1 = new Rect(1, 1, 1, 1);
$o2 = new Rect(2, 2, 1, 1);
$o3 = new Rect(6, 6, 5, 5);
$q->insertObj($o1);
$q->insertObj($o2);
$q->insertObj($o3);

//var_dump($q);

// check collis
$o4 = new Rect(1, 1, 1, 1);

var_dump($q->retrieve($o4));
