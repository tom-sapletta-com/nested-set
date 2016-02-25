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
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class SqlNestedSetMapper
{
    /** @var AdapterInterface */
    protected $databaseAdapter;

    /** @var HydratorInterface */
    protected $hydrator;

    /** @var string */
    protected $tableName;

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
     * @return NodeInterface[]
     */
    public function findAll()
    {
        $sql = new Sql($this->databaseAdapter);
        $select = $sql->select($this->tableName);
        $select->order('lft ASC');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

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
    public function getRootNodes()
    {
        $sql = new Sql($this->databaseAdapter);
        $select = $sql->select($this->tableName);
        $select->where('parent_id IS NULL');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $this->getResultArray($result);
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
        return array_merge($this->getAncestors(),[$node],$this->getDescendants());
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
        $sql = new Sql($this->databaseAdapter);

        $select = $sql->select($this->tableName);
        $select->columns('*');
        $select->where('root_id = :root_id');
        $select->where('lft < :lft');
        $select->where('rgt > :rgt');
        $select->order('lft');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute([
            ':root_id' => $node->getRootId(),
            ':lft' => $node->getLft(),
            ':rgt' => $node->getRgt(),
        ]);

        return $this->getResultArray($result);
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
        $sql = new Sql($this->databaseAdapter);

        $select = $sql->select();
        $select->from($this->tableName);
        $select->where('id = :parent_id');
        $select->order('lft');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute([':parent_id' => $node->getParentId()]);

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
    public function getDescendants(NodeInterface $node)
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
            ':rgt' => $node->getRgt(),
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
     *
     * @return NodeInterface[]
     */
    public function getSiblings(NodeInterface $node)
    {
        $sql = new Sql($this->databaseAdapter);

        $select = $sql->select();
        $select->from($this->tableName);
        $select->where('level = :level');
        $select->where('root_id = :root_id');
        $select->where('id <> :id');
        $select->order('lft');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute([':id' => $node->getId(), ':level' => $node->getLevel(), ':root_id' => $node->getRootId()]);

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
     * @return null|object
     */
    private function getResultRow(ResultInterface $result)
    {
        if ($result->isQueryResult() && $result->getAffectedRows()) {
            $resultData = $result->current();

            return $this->hydrator->hydrate($resultData, $this->serviceLocator->get('HenrikThesing\NestedSet\Entity\NodeInterface'));
        }

        return null;
    }
}