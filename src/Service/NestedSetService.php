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

namespace HenrikThesing\NestedSet\Service;

use HenrikThesing\NestedSet\Entity\NodeInterface;
use HenrikThesing\NestedSet\Mapper\SqlNestedSetMapper;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class NestedSetService implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /** @var SqlNestedSetMapper $mapper */
    protected $mapper;

    /**
     * @param SqlNestedSetMapper $mapper
     */
    public function __construct(SqlNestedSetMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Returns a NodeInterface entity by ID.
     *
     * @param int $id
     *
     * @return NodeInterface|null
     */
    public function find($id)
    {
        $params = compact('id');
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params,function($v) {
            return ($v instanceof NodeInterface);
        });
        if($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->find($id);

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post',$this,$params);

        return $result;
    }

    /**
     * Returns an array of NodeInterface entities.
     *
     * @return NodeInterface[]
     */
    public function findAll()
    {
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this);
        if($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->findAll();

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post',$this,$params);

        return $result;
    }

    /**
     * Returns an array of NodeInterface entities for the given root id
     * or null in case of there is no root_id with the given value.
     *
     * @param int $root_id
     *
     * @return NodeInterface[]|null
     */
    public function findAllByRootId($root_id)
    {
        $params = compact('root_id');
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params);
        if($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->findAllByRootId($root_id);

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post',$this,$params);

        return $result;
    }

    /**
     * Returns an array of NodeInterface entities of all root nodes or
     * null in case of there area no root nodes.
     *
     * @return NodeInterface[]|null
     */
    public function getRootNodes()
    {
        return $this->mapper->getRootNodes();
    }

    /**
     * Returns an array of all NodeInterface entities of the whole branch containing
     * the given NodeInterface entity.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]
     */
    public function getBranch(NodeInterface $node)
    {
        return $this->mapper->getBranch($node);
    }

    /**
     * Returns an array of NodeInterface entities of all ancestor nodes of the given
     * NodeInterface entity or null in case of $node is the root node.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]|null
     */
    public function getAncestors(NodeInterface $node)
    {
        return $this->mapper->getAncestors($node);
    }

    /**
     * Returns the parent NodeInterface entity of the given NodeInterface entity
     * or null in case of $node is the root node.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]|null
     */
    public function getParent(NodeInterface $node)
    {
        return $this->mapper->getParent($node);
    }

    /**
     * Returns an array of NodeInterface entities of all descendant nodes of the given
     * NodeInterface entity or null in case of $node has no descendants.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]|null
     */
    public function getDescendants(NodeInterface $node)
    {
        return $this->mapper->getDescendants($node);
    }

    /**
     * Returns the child NodeInterface entity of the given NodeInterface entity
     * or null in case of $node has no child node.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]|null
     */
    public function getChildren(NodeInterface $node)
    {
        return $this->mapper->getChildren($node);
    }

    /**
     * Returns an array of NodeInterface entities of all siblings of the given
     * NodeInterface entity.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]
     */
    public function getSiblings(NodeInterface $node)
    {
        return $this->mapper->getSiblings($node);
    }

    /**
     * Returns an array of NodeInterface entities of all path nodes from the root
     * to the given NodeInterface entity
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]
     */
    public function getPath(NodeInterface $node)
    {
        return $this->mapper->getPath($node);
    }

}