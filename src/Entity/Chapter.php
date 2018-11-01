<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChapterRepository")
 */
class Chapter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $numChapter;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\ManyToMany(targetEntity="Chapter", cascade={"persist", "remove"}, inversedBy="children", fetch="LAZY")
     * @ORM\JoinTable(name="pathing_chapters")
     */
    private $parents;

    /**
     * @ORM\ManyToMany(targetEntity="Chapter", mappedBy="parents", fetch="LAZY")
     */
    private $children;


    //--------------------------


    /**
     * Chapter constructor.
     */
    public function __construct()
    {
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
    }


    //--------------------------


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNumChapter()
    {
        return $this->numChapter;
    }

    /**
     * @param mixed $numChapter
     */
    public function setNumChapter($numChapter): void
    {
        $this->numChapter = $numChapter;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @param mixed $parents
     */
    public function setParents($parents): void
    {
        $this->parents = $parents;
    }

    /**
     * @param mixed $parent
     */
    public function addParent($parent): void
    {
        /** @var  $parentsList ArrayCollection*/
        $parentsList = $this->getParents();
        $parentsList->add($parent);
        $this->parents = $parentsList;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children): void
    {
        $this->children = $children;
    }

    /**
     * @param mixed $child
     */
    public function addChild($child): void
    {
        /** @var  $childrenList ArrayCollection*/
        $childrenList = $this->getChildren();
        $childrenList->add($child);
        $this->children = $childrenList;
    }


}
