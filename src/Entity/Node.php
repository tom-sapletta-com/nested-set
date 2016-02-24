<?php

/**
 * This file is part of NestedSet.
 *
 * (c) Henrik Thesing <mail@henrikthesing.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Henrik Thesing <mail@henrikthesing.de>
 */

namespace HenrikThesing\NestedSet\Entity;

class Node implements NodeInterface
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $root_id;

    /** @var int */
    protected $parent_id;

    /** @var string */
    protected $name;

    /** @var int */
    protected $lft;

    /** @var int */
    protected $rgt;

    /** @var int */
    protected $level;

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRootId()
    {
        return $this->root_id;
    }

    /**
     * {@inheritDoc}
     */
    public function setRootId($root_id)
    {
        $this->root_id = $root_id;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * {@inheritDoc}
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * {@inheritDoc}
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * {@inheritDoc}
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritDoc}
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
}