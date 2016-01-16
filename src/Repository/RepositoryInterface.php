<?php

namespace Inkl\EntityManager\Repository;

use Inkl\EntityManager\Collection\CollectionInterface;
use Inkl\EntityManager\Entity\EntityInterface;
use Inkl\EntityManager\Factory\FactoryInterface;
use Kir\MySQL\Databases\MySQL;
use Zend\Hydrator\HydratorInterface;
use Doctrine\DBAL\Connection;

interface RepositoryInterface {

	/** @return string */
	public function getMainTable();

	/** @return string */
	public function getPrimaryKey();

	/** @return Mysql */
	public function getMysql();

	/** @return FactoryInterface */
	public function getFactory();

	/** @return HydratorInterface */
	public function getHydrator();

	/**
	 * @param int $id
	 * @return EntityInterface
	 */
	public function load($id);

	/**
	 * @param EntityInterface $entity
	 * @return bool
	 */
	public function save(EntityInterface $entity);


	/**
	 * @return CollectionInterface
	 */
	public function find();


	/**
	 * @param EntityInterface $entity
	 * @return bool
	 */
	public function delete(EntityInterface $entity);

}
