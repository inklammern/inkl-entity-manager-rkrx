<?php

namespace Inkl\EntityManager\Collection;

interface CollectionInterface
{
	public function getQueryBuilder();

	public function getFirst();

	public function getIterator();

	public function setPage($pageNum, $pageSize);
}
