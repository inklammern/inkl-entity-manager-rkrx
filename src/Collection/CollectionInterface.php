<?php

namespace Inkl\EntityManager\Collection;

interface CollectionInterface
{
	public function getSelect();

	public function getFirst();

	public function getIterator();

	public function setPage($pageNum, $pageSize);
}
