<?php

namespace Inkl\EntityManager\Repository;

use Inkl\EntityManager\Collection\BaseCollection;
use Inkl\EntityManager\Entity\EntityInterface;
use Inkl\EntityManager\Factory\FactoryInterface;
use Kir\MySQL\Databases\MySQL;
use Zend\Hydrator\HydratorInterface;


abstract class AbstractRepository implements RepositoryInterface
{

	/** @var MySQL */
	private $mysql;
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
	 * @param MySQL $mysql
	 * @param FactoryInterface $factory
	 * @param HydratorInterface $hydrator
	 * @param $mainTable
	 * @param $primaryKey
	 */
	public function __construct(MySQL $mysql, FactoryInterface $factory, HydratorInterface $hydrator, $mainTable, $primaryKey)
	{
		$this->mysql = $mysql;
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


	public function getMysql()
	{
		return $this->mysql;
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

		$primaryKey = $this->getPrimaryKey();

		$select = $this->mysql->select()
			->from('main_table', $this->getMainTable())
			->where('main_table.' . $primaryKey . '=?', $id);

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
			$this->mysql->update()
				->table('main_table', $this->getMainTable())
				->setAll($data)
				->where('main_table.' . $primaryKey . '=?', $data[$primaryKey])
				->run();

		} else
		{

			// insert
			$this->mysql->insert()
				->into($this->getMainTable())
				->addAll($data)
				->run();

			// last_insert id
			$data[$primaryKey] = $this->mysql->getLastInsertId();
			$this->hydrator->hydrate($data, $entity);

		}

		return true;
	}


	public function getLastInsertId()
	{
		return $this->mysql->getLastInsertId();
	}


	public function delete(EntityInterface $entity)
	{

		$primaryKey = $this->getPrimaryKey();
		$data = $this->hydrator->extract($entity);

		if (isset($data[$primaryKey]) && $data[$primaryKey] > 0)
		{
			$this->mysql->delete()
				->from('main_table', $this->getMainTable())
				->where('main_table.' . $primaryKey . '=?', $data[$primaryKey])
				->run();

			return true;
		}

		return false;
	}

}
