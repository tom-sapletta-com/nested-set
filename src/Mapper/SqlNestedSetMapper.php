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

namespace HenrikThesing\NestedSet\Mapper;

use HenrikThesing\NestedSet\Entity\NodeInterface;
use HenrikThesing\NestedSet\Exception\InvalidNodeIdException;
use HenrikThesing\NestedSet\Hydrator\MapperNamingStrategy;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Hydrator\HydratorInterface;

class SqlNestedSetMapper
{
    /** @var AdapterInterface */
    protected $databaseAdapter;

    /** @var HydratorInterface */
    protected $hydrator;

    /** @var string */
    protected $tableName;

    /** @var NodeInterface */
    protected $baseNode;

    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param AdapterInterface $databaseAdapter
     * @param HydratorInterface $hydrator
     * @param string $tableName
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, AdapterInterface $databaseAdapter, HydratorInterface $hydrator, $tableName)
    {
        $this->serviceLocator = $serviceLocator;
        $this->databaseAdapter = $databaseAdapter;
        $this->hydrator = $hydrator;
        $this->setTableName($tableName);
        $this->hydrator->setNamingStrategy(new MapperNamingStrategy());
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param NodeInterface $baseNode
     */
    private function setBaseNode(NodeInterface $baseNode)
    {
        $this->baseNode = $baseNode;
    }

    /**
     * @return NodeInterface
     * @throws InvalidNodeIdException
     */
    public function findBaseNode()
    {
        if ($this->baseNode === null) {

            $sql = new Sql($this->databaseAdapter);
            $select = $sql->select($this->tableName);
            $select->where('root_id IS NULL');
            $select->where('parent_id IS NULL');

            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $baseNode = $this->getResultRow($result);

            if ($baseNode === null) {
                throw new InvalidNodeIdException('invalid base node id');
            }

            $this->setBaseNode($baseNode);
        }

        return $this->baseNode;
    }

    /**
     * Returns a NodeInterface entity by ID.
     *
     * @param int $id
     * @return NodeInterface|null
     * @throws InvalidNodeIdException
     */
    public function find($id)
    {
        if (!is_int($id) || $id <= 0) {
            throw new InvalidNodeIdException('Id is empty or not an integer');
        }

        $sql = new Sql($this->databaseAdapter);
        $select = $sql->select($this->tableName);
        $select->where('id = :id');
        $select->order('lft ASC');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute([':id' => $id]);

        return $this->getResultRow($result);
    }

    /**
     * Returns an array of NodeInterface entities.
     *
     * @param $includeBaseNode
     *
     * @return NodeInterface[]
     */
    public function findAll($includeBaseNode)
    {
        $sql = new Sql($this->databaseAdapter);
        $select = $sql->select($this->tableName);
        $params = $this->expandSelectWithBaseNodeLogic($select, $includeBaseNode);
        $select->order('lft ASC');

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute($params);

        return $this->getResultArray($result);
    }

    /**
     * Returns an array of NodeInterface entities for the given root id
     * or null in case of there is no root_id with the given value.
     *
     * @param int $root_id
     * @return NodeInterface[]|null
     * @throws InvalidNodeIdException
     */
    public function findAllByRootId($root_id)
    {
        if (!is_int($root_id) || $root_id <= 0) {
            throw new InvalidNodeIdException('Root Id is empty or not an integer');
        }

        $sql = new Sql($this->databaseAdapter);
        $select = $sql->select($this->tableName);
        $select->where('root_id = :root_id');
        $select->order('lft ASC');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute([':root_id' => $root_id]);

        return $this->getResultArray($result);
    }

    /**
     * Returns an array of NodeInterface entities of all root nodes or
     * null in case of there area no root nodes.
     *
     * @return NodeInterface[]|null
     */
    public function findRootNodes()
    {
        $sql = new Sql($this->databaseAdapter);
        $select = $sql->select($this->tableName);
        $select->where('level = 2');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $this->getResultArray($result);
    }

    /**
     * Returns an array of all NodeInterface entities of the whole branch containing
     * the given NodeInterface entity.
     *
     * @param NodeInterface $node
     * @param bool $includeBaseNode
     *
     * @return \HenrikThesing\NestedSet\Entity\NodeInterface[]
     */
    public function findBranch(NodeInterface $node, $includeBaseNode)
    {
        $ancestors = $this->findAncestors($node, $includeBaseNode);
        $descendants = $this->findDescendants($node);

        if (!is_array($ancestors)) {
            $ancestors = [];
        }
        if (!is_array($descendants)) {
            $descendants = [];
        }

        return array_merge($ancestors, [$node], $descendants);
    }

    /**
     * Returns an array of NodeInterface entities of all ancestor nodes of the given
     * NodeInterface entity or null in case of $node is the root node.
     *
     * @param NodeInterface $node
     * @param bool $includeBaseNode
     *
     * @return \HenrikThesing\NestedSet\Entity\NodeInterface[]|null
     */
    public function findAncestors(NodeInterface $node, $includeBaseNode)
    {
        $sql = new Sql($this->databaseAdapter);

        $select = $sql->select($this->tableName);
        $select->where('lft < :lft');
        $select->where('rgt > :rgt');
        $select->order('lft');

        $params = array_merge([
            ':lft' => $node->getLft(),
            ':rgt' => $node->getRgt()
        ], $this->expandSelectWithBaseNodeLogic($select, $includeBaseNode));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($params);

        return $this->getResultArray($result);
    }

    /**
     * Returns the parent NodeInterface entity of the given NodeInterface entity
     * or null in case of $node is the root node.
     *
     * @param NodeInterface $node
     * @param bool $includeBaseNode
     *
     * @return \HenrikThesing\NestedSet\Entity\NodeInterface[]|null
     */
    public function getParent(NodeInterface $node, $includeBaseNode)
    {
        $sql = new Sql($this->databaseAdapter);

        $select = $sql->select();
        $select->from($this->tableName);
        $select->where('id = :parent_id');
        $select->order('lft');

        $params = array_merge([
            ':parent_id' => $node->getParentId()
        ], $this->expandSelectWithBaseNodeLogic($select, $includeBaseNode));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($params);

        return $this->getResultRow($result);
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
        $sql = new Sql($this->databaseAdapter);

        $select = $sql->select($this->tableName);
        $select->where('root_id = :root_id');
        $select->where('lft > :lft');
        $select->where('rgt < :rgt');
        $select->order('lft');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute([
            ':root_id' => $node->getRootId(),
            ':lft' => $node->getLft(),
            ':rgt' => $node->getRgt()
        ]);

        return $this->getResultArray($result);
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
        $sql = new Sql($this->databaseAdapter);

        $select = $sql->select();
        $select->from($this->tableName);
        $select->where('parent_id = :id');
        $select->order('lft');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute([':id' => $node->getId()]);

        return $this->getResultArray($result);
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
        $sql = new Sql($this->databaseAdapter);

        $params = [
            ':level' => $node->getLevel(),
            ':root_id' => $node->getRootId()
        ];

        $select = $sql->select();
        $select->from($this->tableName);
        $select->where('level = :level');
        $select->where('root_id = :root_id');
        if ($includeCurrent === false) {
            $select->where('id <> :id');
            $params['id'] = $node->getId();
        }
        $select->order('lft');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute($params);

        return $this->getResultArray($result);
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
        $sql = new Sql($this->databaseAdapter);

        $select = $sql->select();
        $select->columns(['t2' => '*']);
        $select->from(['t2' => $this->tableName]);
        $select->join(['t1' => $this->tableName], 't1.lft BETWEEN t2.lft AND t2.rgt', []);
        $select->where('t1.root_id = :leaf_node_root_id');
        $select->where('t1.id = :leaf_node_id');
        $select->order('t1.lft');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute([':leaf_node_root_id' => $node->getRootId(), ':leaf_node_id' => $node->getId()]);

        return $this->getResultArray($result);
    }

    /**
     * Returns the result as an array of NodeInterface entities or null.
     *
     * @param $result
     * @return array|null
     */
    private function getResultArray(ResultInterface $result)
    {
        if ($result->isQueryResult() && $result->getAffectedRows()) {
            $results = [];
            foreach ($result as $resultData) {
                $results[] = $this->hydrator->hydrate($resultData, $this->serviceLocator->get('HenrikThesing\NestedSet\Entity\NodeInterface'));
            }
            return $results;
        }

        return null;
    }

    /**
     * Returns the result as a NodeInterface entity or null.
     *
     * @param $result
     * @return null|NodeInterface
     */
    private function getResultRow(ResultInterface $result)
    {
        if ($result->isQueryResult() && $result->getAffectedRows()) {
            $resultData = $result->current();

            return $this->hydrator->hydrate($resultData, $this->serviceLocator->get('HenrikThesing\NestedSet\Entity\NodeInterface'));
        }

        return null;
    }

    /**
     * @param Select $select
     * @param bool $includeBaseNode
     *
     * @return array
     * @throws InvalidNodeIdException
     */
    private function expandSelectWithBaseNodeLogic(Select $select, $includeBaseNode)
    {
        $params = [];
        if ($includeBaseNode === false) {
            $select->where('id <> :idbasenode');
            $params['idbasenode'] = $this->findBaseNode()->getId();
        }
        return $params;
    }
}