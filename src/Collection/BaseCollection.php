<?php

namespace Inkl\EntityManager\Collection;

use Inkl\EntityManager\Repository\RepositoryInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Kir\MySQL\Builder\Select;

class BaseCollection implements CollectionInterface, \IteratorAggregate {

	protected $items = [];

	/** @var RepositoryInterface */
	protected $repository;

	/** @var Select */
	protected $select;

	/**
	 * @param RepositoryInterface $repository
	 */
	public function __construct(RepositoryInterface $repository) {
		$this->repository = $repository;

		$this->initSelect();
	}


	protected function initSelect() {

		$this->select = $this->repository->getMysql()->select()
			->from('main_table', $this->repository->getMainTable());
	}


	protected function loadData()
	{
		$this->items = [];

		$rows = $this->select->fetchRows();
		foreach ($rows as $data)
		{
			$this->items[] = $this->repository->getHydrator()->hydrate($data, $this->repository->getFactory()->create());
		}
	}


	public function getSelect()
	{
		return $this->select;
	}


	public function getFirst()
	{
		$this->loadData();

		return current($this->items);
	}


	public function getCount()
	{
		$select = clone $this->select;

		$select->field('COUNT(*)');

		return $select->fetchValue();
	}


	public function getIterator()
	{
		$this->loadData();

		return new \ArrayIterator($this->items);
	}


	public function setPage($pageNum, $pageSize) {
		$this->select
			->offset(($pageNum-1) * $pageSize)
			->limit($pageSize);

		return $this;
	}

}