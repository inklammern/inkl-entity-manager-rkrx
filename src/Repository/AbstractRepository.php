<?php

namespace Inkl\EntityManager\Repository;

use Inkl\EntityManager\Collection\BaseCollection;
use Inkl\EntityManager\Entity\EntityInterface;
use Inkl\EntityManager\Factory\FactoryInterface;
use Kir\MySQL\Database;
use Zend\Hydrator\HydratorInterface;


abstract class AbstractRepository implements RepositoryInterface
{

	/** @var Database */
	private $db;
	/** @var FactoryInterface */
	private $factory;
	/** @var HydratorInterface */
	private $hydrator;
	/** @var string */
	private $mainTable;
	/** @var string */
	private $primaryKey;

	/**
	 * VideoRepository constructor.
	 * @param Database $db
	 * @param FactoryInterface $factory
	 * @param HydratorInterface $hydrator
	 * @param $mainTable
	 * @param $primaryKey
	 */
	public function __construct(Database $db, FactoryInterface $factory, HydratorInterface $hydrator, $mainTable, $primaryKey)
	{
		$this->db = $db;
		$this->factory = $factory;
		$this->hydrator = $hydrator;
		$this->mainTable = $mainTable;
		$this->primaryKey = $primaryKey;
	}


	public function getMainTable()
	{
		return $this->mainTable;
	}


	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}


	public function getDb()
	{
		return $this->db;
	}


	public function getFactory()
	{
		return $this->factory;
	}


	public function getHydrator()
	{
		return $this->hydrator;
	}


	public function create()
	{
		return $this->factory->create();
	}


	public function load($id)
	{
		return $this->loadByField($this->primaryKey, $id);
	}


	protected function loadByField($field, $value)
	{
		$select = $this->db->select()
			->from('main_table', $this->getMainTable())
			->where('main_table.' . $field . '=?', $value);

		$entity = $this->create();

		if ($data = $select->fetchRow())
		{
			$this->hydrator->hydrate($data, $entity);
		}

		return $entity;
	}


	/**
	 * @return BaseCollection
     */
	public function find()
	{
		return new BaseCollection($this);
	}


	public function save(EntityInterface $entity)
	{
		$primaryKey = $this->getPrimaryKey();
		$data = $this->hydrator->extract($entity);

		if (isset($data[$primaryKey]) && !empty($data[$primaryKey]))
		{
			// update
			$this->db->update()
				->table('main_table', $this->getMainTable())
				->setAll($data)
				->where('main_table.' . $primaryKey . '=?', $data[$primaryKey])
				->run();

		} else
		{

			// insert
			$this->db->insert()
				->into($this->getMainTable())
				->addAll($data)
				->run();

			// last_insert id
			$data[$primaryKey] = $this->db->getLastInsertId();
			$this->hydrator->hydrate($data, $entity);

		}

		return true;
	}


	public function getLastInsertId()
	{
		return $this->db->getLastInsertId();
	}


	public function delete(EntityInterface $entity)
	{

		$primaryKey = $this->getPrimaryKey();
		$data = $this->hydrator->extract($entity);

		if (isset($data[$primaryKey]) && $data[$primaryKey] > 0)
		{
			$this->db->delete()
				->from('main_table', $this->getMainTable())
				->where('main_table.' . $primaryKey . '=?', $data[$primaryKey])
				->run();

			return true;
		}

		return false;
	}

}
