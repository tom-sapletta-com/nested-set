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

interface NodeInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getRootId();

    /**
     * @param $root_id
     *
     * @return $this
     */
    public function setRootId($root_id);

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param $parent_id
     *
     * @return $this
     */
    public function setParentId($parent_id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getLft();

    /**
     * @param int $lft
     *
     * @return $this
     */
    public function setLft($lft);

    /**
     * @return int
     */
    public function getRgt();

    /**
     * @param int $rgt
     *
     * @return $this
     */
    public function setRgt($rgt);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param int $level
     *
     * @return $this
     */
    public function setLevel($level);
}