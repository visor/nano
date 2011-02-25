<?php

class ActiveRecord_Relation {

	const RELATION_SEPARATOR = '::';

	/**
	 * @var array
	 */
	private static $objects = array();

	/**
	 * @var array
	 */
	private static $relations = array();

	/**
	 * @var array
	 */
	private static $childs = array();

	/**
	 * @var array
	 */
	private static $parents = array();

	/**
	 * @var array[string]
	 */
	private static $related = array();

	/**
	 * @return ActiveRecord
	 * @param ActiveRecord $record
	 * @param string $relation
	 * @throws ActiveRecord_Exception_UnknownRelation
	 */
	public static function getRecord(ActiveRecord $record, $relationName) {
		$relation = $record->getRelation($relationName);
		if (null === $relation) {
			throw new ActiveRecord_Exception_UnknownRelation($relationName, $record);
		}
		$hash = spl_object_hash($record);
		if (!isSet(self::$objects[$hash])) {
			self::$objects[$hash] = $record;
		}
		if (isSet(self::$relations[$hash][$relationName])) {
			$parentHash = self::$relations[$hash][$relationName];
			return self::$objects[$parentHash];
		}

		$class = $relation[ActiveRecord::REL_CLASS];
		$value = $record->__get($relation[ActiveRecord::REL_FIELD]);
		if (null === $value) {
			$parent = $class::instance();
		} else {
			$field  = $relation[ActiveRecord::REL_REF];
			$parent = $class::prototype()->findOne(array($field => $value));
		}
		if (!$parent) {
			throw new ActiveRecord_Exception_RelationTargetNotFound($relation, $record);
		}

		self::storeRelatedRecord($parent, $hash, $relationName);
		return $parent;
	}

	/**
	 * @return void
	 * @param ActiveRecord $record
	 * @param string $relationName
	 * @param ActiveRecord $value
	 * @throws ActiveRecord_Exception_UnknownRelation
	 */
	public static function setRecord(ActiveRecord $record, $relationName, ActiveRecord $value) {
		$relation = $record->getRelation($relationName);
		if (null === $relation) {
			throw new ActiveRecord_Exception_UnknownRelation($relationName, $record);
		}
		$hash    = spl_object_hash($record);
		$newHash = spl_object_hash($value);
		$oldHash = self::findParentHashFor($hash, $relationName);
		if ($newHash === $oldHash) {
			return;
		}
		self::clearRelatedInfo($oldHash, $hash, $relationName);
		self::storeRelatedRecord($value, $hash, $relationName);
	}

	public static function updateRelation(ActiveRecord $record) {
		$hash = spl_object_hash($record);
		self::$objects[$hash] = $record;
		if (isSet(self::$childs[$hash])) {
			foreach (self::$childs[$hash] as $childHash => $null) {
				$child = self::$objects[$childHash];
				foreach (self::$related[$hash][$childHash] as $relationName => $null) {
					$relation = $child->getRelation($relationName);
					$child->__set($relation[ActiveRecord::REL_FIELD], $record->__get($relation[ActiveRecord::REL_REF]));
				}
			}
		}
	}

	/**
	 * @return void
	 * @param ActiveRecord $parent
	 * @param string $hash
	 * @param string $relationName
	 */
	protected static function storeRelatedRecord(ActiveRecord $parent, $hash, $relationName) {
		$parentHash = spl_object_hash($parent);
		self::$objects[$parentHash]            = $parent;
		self::$relations[$hash][$relationName] = $parentHash;

		if (!isSet(self::$parents[$hash])) {
			self::$parents[$hash] = array();
		}
		if (!isSet(self::$childs[$parentHash])) {
			self::$childs[$parentHash]  = array();
			self::$related[$parentHash] = array();
		}
		if (!isSet(self::$related[$parentHash][$hash])) {
			self::$related[$parentHash][$hash] = array();
		}
		self::$parents[$hash][$relationName] = $parentHash;
		self::$childs[$parentHash][$hash] = null;
		self::$related[$parentHash][$hash][$relationName] = null;
	}

	/**
	 * @return string
	 * @param string $childHash
	 * @param string $relationName
	 */
	protected static function findParentHashFor($childHash, $relationName) {
		if (!isSet(self::$parents[$childHash][$relationName])) {
			return null;
		}
		return self::$parents[$childHash][$relationName];
	}

	/**
	 * @return void
	 * @param string $parentHash
	 * @param string $hash
	 * @param string $relationName
	 */
	protected static function clearRelatedInfo($parentHash, $hash, $relationName) {
		unSet(self::$childs[$parentHash][$hash]);
		unSet(self::$parents[$hash][$parentHash]);
		unSet(self::$related[$parentHash][$hash][$relationName]);
		unSet(self::$relations[$hash][$relationName]);
	}

}