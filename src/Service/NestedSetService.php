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
use HenrikThesing\NestedSet\Exception\InvalidNodeIdException;
use HenrikThesing\NestedSet\Mapper\SqlNestedSetMapper;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class NestedSetService implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /** @var SqlNestedSetMapper $mapper */
    protected $mapper;

    /** @var  bool */
    protected $includeBaseNode = false;

    /**
     * @param SqlNestedSetMapper $mapper
     */
    public function __construct(SqlNestedSetMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return boolean
     */
    public function getIncludeBaseNode()
    {
        return $this->includeBaseNode;
    }

    /**
     * @param boolean $includeBaseNode
     */
    public function setIncludeBaseNode($includeBaseNode)
    {
        $this->includeBaseNode = $includeBaseNode;
    }

    /**
     * @return NodeInterface
     * @throws InvalidNodeIdException
     */
    public function findBaseNode()
    {
        return $this->mapper->findBaseNode();
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
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params, function($v) {
            return ($v instanceof NodeInterface);
        });
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->find($id);

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

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
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->findAll($this->getIncludeBaseNode());

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

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
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->findAllByRootId($root_id);

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $result;
    }

    /**
     * Returns an array of NodeInterface entities of all root nodes or
     * null in case of there area no root nodes.
     *
     * @return NodeInterface[]|null
     */
    public function findRootNodes()
    {
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this);
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->findRootNodes();

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $result;
    }

    /**
     * Returns an array of all NodeInterface entities of the whole branch containing
     * the given NodeInterface entity.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]
     */
    public function findBranch(NodeInterface $node)
    {
        $params = compact('node');
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params);
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->findBranch($node, $this->getIncludeBaseNode());

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $result;
    }

    /**
     * Returns an array of NodeInterface entities of all ancestor nodes of the given
     * NodeInterface entity or null in case of $node is the root node.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]|null
     */
    public function findAncestors(NodeInterface $node)
    {
        $params = compact('node');
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params);
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->findAncestors($node, $this->getIncludeBaseNode());

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $result;
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
        $params = compact('node');
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params, function($v) {
            return ($v instanceof NodeInterface);
        });
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->getParent($node, $this->getIncludeBaseNode());

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $result;
    }

    /**
     * Returns an array of NodeInterface entities of all descendant nodes of the given
     * NodeInterface entity or null in case of $node has no descendants.
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface[]|null
     */
    public function findDescendants(NodeInterface $node)
    {
        $params = compact('node');
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params);
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->findDescendants($node);

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $result;
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
        $params = compact('node');
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params);
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->getChildren($node);

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $result;
    }

    /**
     * Returns an array of NodeInterface entities of all siblings of the given
     * NodeInterface entity.
     *
     * @param NodeInterface $node
     * @param bool $includeCurrent
     *
     * @return NodeInterface[]
     */
    public function findSiblings(NodeInterface $node, $includeCurrent = false)
    {
        $params = compact('node');
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params);
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->findSiblings($node, $includeCurrent);

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $result;
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
        $params = compact('node');
        $event = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this, $params);
        if ($event->stopped()) {
            return $event->last();
        }

        $result = $this->mapper->getPath($node);

        $params['__RESULT__'] = $result;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $result;
    }
}