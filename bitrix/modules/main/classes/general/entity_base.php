<?php

abstract class CBaseEntity
{
	protected
		$className,
		$name,
		$dbTableName,
		$primary;

	protected
		$uf_id;

	protected
		$fieldsMap,
		$fields,
		$u_fields;

	protected
		$tmp_aliases; // temprorary holds aliases after last query (GetList)

	protected
		$references;

	protected
		$filePath;

	public static
		$lEsc,
		$rEsc;

	protected static
		$instances;


	public static function GetInstance($entityName)
	{
		return self::getInstanceDirect('C' . $entityName . 'Entity');
	}


	protected static function GetInstanceDirect($className)
	{
		if (empty(self::$instances[$className]))
		{
			self::$instances[$className] = new $className;
			self::$instances[$className]->Initialize();
			self::$instances[$className]->PostInitialize();
		}

		return self::$instances[$className];
	}


	public function PostInitialize()
	{
		global $DBType;

		// базовые свойства
		$this->name = substr($this->className, 1, -6);
		//$this->dbTableName = strtolower($this->name);
		$this->dbTableName = empty($this->dbTableName)
			? 'b_' . self::camel2snake($this->name)
			: $this->dbTableName;
		$this->primary = array('ID');
		$this->references = array();

		if (empty($this->filePath))
		{
			throw new Exception(sprintf(
				'Parameter `filePath` required for `%s` Entity', $this->name
			));
		}

		// инициализация атрибутов
		foreach ($this->fieldsMap as $fieldName => &$fieldInfo)
		{
			if (!empty($fieldInfo['reference']))
			{
				$refEntity = CBaseEntity::getInstance($fieldInfo['data_type']);
				$field = new CReferenceEntityField($fieldName, $this, $refEntity, $fieldInfo['reference'][0], $fieldInfo['reference'][1]);

				// кеш ссылок
				$this->references[strtolower($fieldInfo['data_type'])][] = $field;
			}
			elseif (!empty($fieldInfo['expr']))
			{
				$field = new CExprEntityField($fieldName, $fieldInfo['data_type'], $this, $fieldInfo['expr'], $fieldInfo);
			}
			elseif (!empty($fieldInfo['USER_TYPE_ID']))
			{
				$field = new CUEntityField($fieldInfo, $this);
			}
			else
			{
				$field = new CEntityField($fieldName, $fieldInfo['data_type'], $this, $fieldInfo);
			}

			$this->fields[$fieldName] = $field;

			// add reference field for UField iblock_section
			if ($field instanceof CUEntityField && $field->getTypeId() == 'iblock_section')
			{
				$refFieldName = $field->GetName().'_BY';

				if ($field->isMultiple())
				{
					$localFieldName = $field->getValueFieldName();
				}
				else
				{
					$localFieldName = $field->GetName();
				}

				$newFieldInfo = array(
					'data_type' => 'IblockSection',
					'reference' => array($localFieldName, 'ID')
				);

				$refEntity = CBaseEntity::getInstance($newFieldInfo['data_type']);
				$newRefField = new CReferenceEntityField($refFieldName, $this, $refEntity, $newFieldInfo['reference'][0], $newFieldInfo['reference'][1]);

				$this->fields[$refFieldName] = $newRefField;
			}
		}

		if (empty(self::$lEsc))
		{
			if ($DBType == 'mysql')
			{
				self::$lEsc = '`';
				self::$rEsc = '`';
			}
			elseif ($DBType == 'mssql')
			{
				self::$lEsc = '[';
				self::$rEsc = ']';
			}
			elseif ($DBType == 'oracle')
			{
				self::$lEsc = '"';
				self::$rEsc = '"';
			}

		}

	}


	final public function GetList($select, $arFilter = array(), $group = array(), $order = array(), $limit = array(), $options = array())
	{
		global $DB;

		// нормализация полей - приведение к объектам
		list($selChains, $objChainsHidden, $chainByAlias, $aliases) = $this->NormalizeSelectDefinition($select);
		$arFilterChains = $this->NormalizeArFilterDefinition($arFilter, $objChainsHidden, $chainByAlias);
		$groupChains = $this->NormalizeGroupDefinition($group, $selChains, $aliases, $chainByAlias);
		$ordChains = $this->NormalizeOrderDefinition($order, $chainByAlias);
		//$this->NormalizeLimitDefinition($limit);

		list($joinInfo, $need_group_by) = $this->NormalizeJoinDefinition($selChains, $arFilterChains, $groupChains, $ordChains, $objChainsHidden);

		// SQL SELECT part
		$bsqlSelect = $this->BuildSelect($selChains, $aliases);
		$sqlSelect = "SELECT\n\t" . $bsqlSelect;

		// SQL FROM part
		$bsqlFrom = sprintf('%s %s%s%s',
			/*self::$lEsc,*/ $this->dbTableName, /*self::$rEsc,*/
			self::$lEsc, self::camel2snake($this->name), self::$rEsc
		);
		$sqlFrom = "\nFROM\n\t" . $bsqlFrom;

		// SQL JOIN part
		$bsqlJoin = $this->BuildJoin($joinInfo);
		$sqlJoin = "\n".$bsqlJoin;

		// SQL WHERE & HAVING part
		list($bsqlWhere, $sqlWhere, $bsqlHaving, $sqlHaving) = array('', '', '', '');

		if (!empty($arFilterChains))
		{
			list($bsqlWhere, $bsqlHaving) = $this->BuildArWhere($arFilterChains, $arFilter, $aliases);

			if (!empty($bsqlWhere))
			{
				$sqlWhere = "\nWHERE".$bsqlWhere;
			}

			if (!empty($bsqlHaving))
			{
				$sqlHaving = "\nHAVING".$bsqlHaving;
			}
		}

		$utmWhere = $this->BuildUtmUserfieldsFilter($selChains, $objChainsHidden);

		if (!empty($utmWhere))
		{
			if (!empty($sqlWhere))
			{
				$sqlWhere .= ' AND ('.$utmWhere.')';
			}
			else
			{
				$sqlWhere = "\nWHERE ".$utmWhere;
			}
		}


		// SQL GROUP BY part
		list($bsqlGroup, $sqlGroup) = array('', '');

		if (!empty($groupChains) || $need_group_by)
		{
			$bsqlGroup = $this->BuildGroup($groupChains, $aliases, $selChains, $ordChains);
			if (!empty($bsqlGroup))
			{
				$sqlGroup = "\nGROUP BY\n\t".$bsqlGroup;
			}
		}

		// SQL ORDER part
		list($bsqlOrder, $sqlOrder) = array('', '');

		if (!empty($ordChains))
		{
			$bsqlOrder = $this->BuildOrder($ordChains, $aliases);
			$sqlOrder = "\nORDER BY\n\t".$bsqlOrder;
		}

		// summary
		$bsql = array($bsqlSelect, $bsqlFrom, $bsqlJoin, $bsqlWhere, $bsqlGroup, $bsqlHaving, $bsqlOrder);
		$sql = array($sqlSelect, $sqlFrom, $sqlJoin, $sqlWhere, $sqlGroup, $sqlHaving, $sqlOrder);

		// options
		foreach ($options as $optName => $optValue)
		{
			$bsql = str_replace('%'.$optName.'%', $optValue, $sql);
			$sql = str_replace('%'.$optName.'%', $optValue, $sql);
		}

		// execute query
		$mainQuery = join('', $sql);

		// remove too long aliases for oracle
		list($mainQuery, $replacedAliases) = self::optimizeAliases($mainQuery);

		if (empty($limit))
		{
			//echo '<pre>'.$mainQuery.'</pre>';
			$result = $DB->query($mainQuery);
			$result->arReplacedAliases = $replacedAliases;
		}
		else if (array_key_exists('nPageTop', $limit))
		{
			//echo '<pre>'.$mainQuery.'</pre>';
			$mainQuery = $DB->TopSql($mainQuery, intval($limit['nPageTop']));
			$result = $DB->query($mainQuery);
			$result->arReplacedAliases = $replacedAliases;
		}
		else
		{
			// count rows
			if (empty($bsqlGroup))
			{
				$sqlSelect = 'SELECT COUNT(\'x\') as '.self::$lEsc.'TMP_ROWS_CNT'.self::$rEsc;
			}
			else
			{
				$sqlSelect = 'SELECT \'x\'';
			}

			$sql[0] = $sqlSelect;		// replace select
			$sql[6] = '';				// clear order
			$cntQuery = join('', $sql);
			$result = $DB->query($cntQuery);

			//echo '<pre>'.$cntQuery.'</pre>';
			//echo '<pre>'.$mainQuery.'</pre>';

			if (empty($bsqlGroup))
			{
				$result = $result->Fetch();
				$cnt = $result["TMP_ROWS_CNT"];
			}
			else
			{
				$cnt = $result->SelectedRowsCount();
			}

			// main query
			$result = new CDBResult();
			$result->arReplacedAliases = $replacedAliases;
			$result->NavQuery($mainQuery, $cnt, $limit);
		}

		$this->tmp_aliases = $aliases;

		return $result;
	}


	// normalize*Definition - приведение информации о полях к стандарту Chain
	protected function NormalizeSelectDefinition($select)
	{
		$selFields = array();
		$selReferences = array();

		// цепочки для select
		$objChains = array();

		// косвенные цепочки. фактически в select нет, но встречаются в запросе
		// в данном случае в expr других полей
		$objChainsHidden = array();

		// коллекция заданных программистом алиасов
		$chainByAlias = array();

		$aliases = array(); // цепочное имя => алиас

		$preAlias = strtoupper(self::camel2snake($this->name)) . '_';
		$preAlias = '';

		foreach ($select as $key => $value)
		{
			if (is_array($value))
			{
				// это runtime составленное поле с алиасом
				$field = new CExprEntityField($key, $value['data_type'], $this, $value['expr']);

				// коллекционирование всех составных атрибутов (рекурсивно)
				//self::CollectExprChains($field, $objChainsHidden);

				$objChain = array(array(
					'tAlias' => '',
					'value' => $field
				));

				// коллекционирование всех составных атрибутов (рекурсивно)
				self::CollectExprChainsNEW($objChain, $objChainsHidden);

				$objChains[] = array('value' => $objChain, 'fAlias' => $key);

				$chainByAlias[$key] = $objChain;

				$aliases[$key] = $key;
			}
			else
			{
				// это определенное в сущности или runtime обычное поле
				// или указание на связь с сущностью
				$objChain = self::GetObjectChain($this, $value);

				// установка алиаса поля
				$lastElem = end($objChain);

				if ($lastElem['value'] instanceof CReferenceEntityField)
				{
					// прямая ссылка на сущность
					$refEntity = $lastElem['value']->getRefEntity();

					// добавление в выборку каждого поля подключаемой сущности
					foreach ($refEntity->GetFields() as $refField)
					{
						if (!($refField instanceof CReferenceEntityField))
						{
							$refChain = $objChain;
							$refChain[] = array(
								'tAlias' => '',
								'value' => $refField
							);

							// алиасинг
							$cleanValue = str_replace('.*', '', $value) . '.' . $refField->GetName();
							$alias = self::GetAliasByChain($refChain, strtoupper(self::camel2snake($this->name)));
							$chainByAlias[$alias] = $refChain;
							$aliases[$cleanValue] = $alias;

							$objChains[$cleanValue] = array('value' => $refChain);
						}
					}
				}
				elseif (is_array ($lastElem['value'])
					&& $lastElem['value'][0] instanceof CBaseEntity
					&& $lastElem['value'][1] instanceof CReferenceEntityField
				)
				{
					// ссылка с другой сущности на предыдущую к ней
					$refEntity = $lastElem['value'][0];

					$cleanEntityValue = str_replace(':'.self::snake2camel($lastElem['value'][1]->GetName()), '', $value);
					$cleanEntityValue = str_replace('.*', '', $cleanEntityValue);
					$cleanEntityValue = strtoupper(self::camel2snake($cleanEntityValue));

					// добавление в выборку каждого поля подключаемой сущности
					foreach ($refEntity->GetFields() as $refField)
					{
						if (!($refField instanceof CReferenceEntityField))
						{
							$refChain = $objChain;
							$refChain[] = array(
								'tAlias' => '',
								'value' => $refField
							);

							// алиасинг
							$cleanValue = str_replace('.*', '', $value) . '.' . $refField->GetName();
							$alias = self::GetAliasByChain($refChain, strtoupper(self::camel2snake($this->name)));
							$chainByAlias[$alias] = $refChain;
							$aliases[$cleanValue] = $alias;

							$objChains[$cleanValue] = array('value' => $refChain);
						}
					}
				}
				elseif ($lastElem['value'] instanceof CBaseEntity)
				{
					// usually it means all fields of current entity
					foreach ($lastElem['value']->GetFields() as $refField)
					{
						if (!($refField instanceof CReferenceEntityField))
						{
							$refChain = array(array(
								'tAlias' => '',
								'value' => $refField
							));

							// алиасинг
							$cleanValue = $refField->GetName();
							$alias = self::GetAliasByChain($refChain, strtoupper(self::camel2snake($this->name)));
							$chainByAlias[$alias] = $refChain;
							$aliases[$cleanValue] = $alias;

							$objChains[$cleanValue] = array('value' => $refChain);
						}
					}
				}
				else
				{
					// определенное в сущности поле
					$alias = is_numeric($key) ? self::GetAliasByChain($objChain, strtoupper(self::camel2snake($this->name))) : $key;
					$objChains[$value] = array('value' => $objChain, 'fAlias' => $alias);

					if (!is_numeric($key))
					{
						$chainByAlias[$key] = $objChain;
					}
					else
					{
						$chainByAlias[$alias] = $objChain;
					}

					$aliases[$value] = $alias;

					// collect buildFrom fields (recursively)
					if ($lastElem['value'] instanceof CExprEntityField)
					{
						//var_dump($objChain);
						//self::CollectExprChains($lastElem['value'], $objChainsHidden);
						self::CollectExprChainsNEW($objChain, $objChainsHidden);
					}
				}

				// обычные runtime поля пока не поддерживаются, в том числе потому что
				// они могут быть приняты за имена Entity при формировании цепочек
			}
		}

		return array($objChains, $objChainsHidden, $chainByAlias, $aliases);
	}


	protected function NormalizeJoinDefinition(&$selChains, &$arFilterChains, &$groupChains, &$ordChains, &$objChainsHidden)
	{
		$joins = array();

		$need_group_by = false;

		// маппинг связей в формате Entity1/RefField/Entity2
		// например, Task/RESPONSIBLE/User
		$joinMap = array();

		// склеивание всех цепочек по ссылкам
		$globChains = array();

		foreach (array('selChains', 'arFilterChains','groupChains', 'ordChains', 'objChainsHidden') as $argName)
		{
			foreach ($$argName as &$chain)
			{
				$globChains[] = &$chain;
			}
		}

		// формирование joinInfo
		foreach ($globChains as &$chain)
		{
			$prevEntity = $this;
			$prevAlias = self::camel2snake($this->GetName());
			$prevAliasPrefix = self::camel2snake($this->GetName());

			$mapKey = '';

			foreach ($chain['value'] as &$chainElem)
			{
				$additionalCondition = array();

				if ($chainElem['value'] instanceof CReferenceEntityField)
				{
					// прямая ссылка на сущность
					$srcEntity = $prevEntity;
					$referenceName = $chainElem['value']->GetName();
					$dstEntity = $chainElem['value']->GetRefEntity();

					$tableAlias = strtolower($chainElem['value']->GetName());
					$tableAlias = $prevAliasPrefix.'_'.$tableAlias;

					$srcFieldName = $chainElem['value']->GetLocalField()->GetName();
					$dstFieldName = $chainElem['value']->GetRemoteField()->GetName();
				}
				elseif (is_array ($chainElem['value'])
					&& $chainElem['value'][0] instanceof CBaseEntity
					&& $chainElem['value'][1] instanceof CReferenceEntityField
				)
				{
					// обратная ссылка с сущности
					$srcEntity = $prevEntity;
					$referenceName = $chainElem['value'][1]->GetName();
					$dstEntity = $chainElem['value'][0];

					$tableAlias = self::camel2snake($chainElem['value'][0]->GetName()) . '_' . strtolower($chainElem['value'][1]->GetName());
					$tableAlias = $prevAliasPrefix.'_'.$tableAlias;

					$srcFieldName = $chainElem['value'][1]->GetRemoteField()->GetName();
					$dstFieldName = $chainElem['value'][1]->GetLocalField()->GetName();

					if ($chainElem['value'][0]->isUtm())
					{
						$additionalCondition = array(
							self::$lEsc . $tableAlias . self::$rEsc . '.' . self::$lEsc . 'FIELD_ID' . self::$rEsc,
							$chainElem['uField']->getFieldId()
						);
					}
				}
				else
				{
					// в данной цепочке связанности закончились
					$chainElem['tAlias'] = $prevAlias;

					// check if need group by
					// just because here is global foreach
					if ($chainElem['value'] instanceof CExprEntityField && $chainElem['value']->IsAggregated())
					{
						$need_group_by = true;
					}

					break;
				}

				// маппинг
				if (empty($mapKey))
				{
					$mapKey = $srcEntity->GetName();
				}

				$mapKey .= '/' . $referenceName . '/' . $dstEntity->GetName();

				if (!array_key_exists($mapKey, $joinMap))
				{
					$join = array(
						'type' => 'LEFT',
						'table' => $dstEntity->getDbTableName(),
						'alias' => $tableAlias,
						'condition' => array(
							array(
								self::$lEsc . $prevAlias . self::$rEsc . '.' . self::$lEsc . $srcFieldName . self::$rEsc,
								self::$lEsc . $tableAlias . self::$rEsc . '.' . self::$lEsc . $dstFieldName . self::$rEsc
							)
						)
					);

					if (!empty($additionalCondition))
					{
						$join['condition'][] = $additionalCondition;
					}

					$joins[] = $join;

					$joinMap[$mapKey] = $tableAlias;
				}

				$chainElem['tAlias'] = $tableAlias;

				$prevEntity = $dstEntity;
				$prevAlias = $tableAlias;
				$prevAliasPrefix = $tableAlias;
			}
		}

		return array($joins, $need_group_by);
	}


	protected function NormalizeArFilterDefinition($arFilter, &$objChainsHidden, $chainByAlias)
	{
		$objChains = array();

		foreach ($arFilter as $strFilter => $filterMatch)
		{
			if ($strFilter === 'LOGIC')
			{
				continue;
			}

			if (!is_numeric($strFilter))
			{
				// got it! it is field name
				// parse CSQLWhere format
				preg_match('/([^a-z0-9_\.]+)(.*)/i', $strFilter, $matches);

				//$filterExpr = $matches[1];
				$strChain = $matches[2];

				$chainValue = array_key_exists($strChain, $chainByAlias)
					? $chainByAlias[$strChain]
					: self::GetObjectChain($this, $strChain);

				$objChains[$strChain] = array(
					'value' => $chainValue,
					//'expr' => $filterExpr,
					//'filter' => $filterMatch
				);

				// fill hidden select
				$lastElem = end($chainValue);

				if ($lastElem['value'] instanceof CExprEntityField)
				{
					self::CollectExprChainsNEW($chainValue, $objChainsHidden);
				}
			}

			if (is_array($filterMatch))
			{
				$objChains = array_merge($objChains, $this->NormalizeArFilterDefinition($filterMatch, &$objChainsHidden, $chainByAlias));
			}


		}

		return $objChains;
	}


	protected function NormalizeGroupDefinition($group, &$selChains, $aliases, $chainByAlias)
	{
		$objChains = array();

		foreach ($group as $strChain)
		{
			$chainValue = array_key_exists($strChain, $chainByAlias)
				? $chainByAlias[$strChain]
				: self::GetObjectChain($this, $strChain);

			$objChains[$strChain] = array('value' => $chainValue);

			// все что есть в GROUP - должно быть в SELECT
//			if (!array_key_exists($strChain, $selChains))
//			{
//				$key = array_search($strChain, $aliases);
//				$key = $key !== false ? $key : $strChain;
//				$selChains[$key] = array('value' => $chainValue);
//			}
		}

		return $objChains;
	}


	protected function NormalizeOrderDefinition($order, $chainByAlias)
	{
		$objChains = array();

		foreach ($order as $key => $value)
		{
			if (is_numeric($key))
			{
				$strChain = $value;
				$sort = 'ASC';
			}
			else
			{
				$strChain = $key;
				$sort = $value;
			}

			$chainValue = array_key_exists($strChain, $chainByAlias)
				? $chainByAlias[$strChain]
				: self::GetObjectChain($this, $strChain);

			$objChains[$strChain] = array('value' => $chainValue, 'sort' => $sort);
		}

		return $objChains;
	}


	protected function BuildUtmUserfieldsFilter($selChains, $objChainsHidden)
	{
		// for INNER JOIN only

		// join cahins
		$globChains = array();

		foreach (array('selChains', 'objChainsHidden') as $argName)
		{
			foreach ($$argName as &$chain)
			{
				$globChains[] = &$chain;
			}
		}
		unset($chain);

		// add where to joins
		$sql = array();
		foreach ($globChains as $chain)
		{
			foreach ($chain['value'] as $chainElem)
			{
				if (!empty($chainElem['uField']) && $chainElem['uField']->isMultiple())
				{
					$clause = sprintf('%s%s%s.%sFIELD_ID%s = %s',
						self::$lEsc, $chainElem['tAlias'], self::$rEsc,
						self::$lEsc, self::$rEsc, (int) $chainElem['uField']->getFieldId()
					);

					//$sql[] = $clause;
				}
			}
		}

		return join(' AND ', array_unique($sql));
	}


	protected function NormalizeLimitDefinition(&$limit)
	{
		if (!is_array($limit))
		{
			$limit = array($limit);
		}
	}


	// build* - построение SQL
	protected function BuildSelect($selChains, $aliases)
	{
		$select = array();

		foreach ($selChains as $key => $chain)
		{
			// ориентир на последние элементы цепочек
			$lastElem = end($chain['value']);

			if ($lastElem['value'] instanceof CExprEntityField)
			{
				$withFieldAlias = !array_key_exists($key, $aliases);
				$sSQL = $lastElem['value']->GetSQLDefinition($withFieldAlias);

				if (!$withFieldAlias)
				{
					$sSQL .= ' AS ' . self::$lEsc . $aliases[$key] . self::$rEsc;
				}
			}
			else
			{
				// last field - usual emtity field
				$sSQL = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);

				// у поля может быть алиас
				//if (array_key_exists($key, $aliases))
				if (array_key_exists($key, $aliases) && $aliases[$key] != $lastElem['value']->GetName())
				{
					$sSQL .= ' AS ' . self::$lEsc . $aliases[$key] . self::$rEsc;
				}
			}

			$select[] = $sSQL;
		}

		return join(",\n\t", $select);
	}


	protected function BuildJoin($joinTables)
	{
		$sql = array();

		foreach ($joinTables as $j)
		{
			$onSql = array();

			foreach ($j['condition'] as $cond)
			{
				$onSql[] = $cond[0].' = '.$cond[1];
			}

			$sql[] = sprintf('%s JOIN %s %s%s%s ON %s',
				$j['type'], $j['table'],
				self::$lEsc, $j['alias'], self::$rEsc,
				join(' AND ', $onSql)
			);
		}

		return join("\n", $sql);
	}


	protected function preBuildArWhere(&$arFilter, &$arFilterChains, &$aliases, &$cswFields, $level = 0, $useHaving = false)
	{
		if ($level == 0)
		{
			$whereFilter = array();
			$havingFilter = array();
		}

		$logic = 'AND';

		foreach ($arFilter as $strFilter => &$filterMatch)
		{
			if ($strFilter === 'LOGIC')
			{
				$logic = $filterMatch;
				continue;
			}

			$is_having = false;
			$arrayKey = $strFilter;

			if (!is_numeric($strFilter))
			{
				// got it! it is field name
				// parse CSQLWhere format
				preg_match('/([^a-z0-9_\.`\[\]"]+)(.*)/i', $strFilter, $matches);

				$filterExpr = $matches[1];
				$strChain = $matches[2];

				// if  level > 0
				if (array_key_exists(strtoupper($strChain), $cswFields))
				{
					// row already prepared
					continue;
				}

				$chain = $arFilterChains[$strChain]['value'];

				$lastElem = end($chain);
				$fieldType = $lastElem['value']->getDataType();

				// rewrite type & value for CSQLWhere
				if ($fieldType == 'integer')
				{
					$fieldType = 'int';
				}
				elseif ($fieldType == 'boolean')
				{
					$fieldType = 'string';
					$filterMatch = $lastElem['value']->normalizeValue($filterMatch);
				}

				$is_having = $lastElem['value'] instanceof CExprEntityField && $lastElem['value']->IsAggregated();

				// get real sql definition
				if (array_key_exists($strChain, $aliases))
				{
					// указано цепочное имя
					$sqlFieldName = $lastElem['value'] instanceof CExprEntityField
						? $lastElem['value']->GetSQLDefinition(false)
						: $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);

				}
				elseif (in_array($strChain, $aliases))
				{
					// указан алиас
					$sqlFieldName = $lastElem['value'] instanceof CExprEntityField
						? $lastElem['value']->GetSQLDefinition(false)
						: $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
				}
				else
				{
					// указанное поле засветилось первый раз - его нет в select
					//$sqlFieldName = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
					$sqlFieldName = $lastElem['value'] instanceof CExprEntityField
						? $lastElem['value']->GetSQLDefinition(false)
						: $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
				}

				// replace in arFilter by real sql definition
				// if (level > 0)
				$arFilter[$filterExpr.strtoupper($sqlFieldName)] = $filterMatch;
				unset($arFilter[$strFilter]);

				$arrayKey = $filterExpr.strtoupper($sqlFieldName);

				// collect cswFields
				$cswFields[strtoupper($sqlFieldName)] = array(
					'TABLE_ALIAS' => $lastElem['tAlias'],
					'FIELD_NAME' => $sqlFieldName,
					'FIELD_TYPE' => $fieldType,
					'MULTIPLE' => '',
					'JOIN' => ''
				);
			}

			if (is_array($filterMatch))
			{
				list($filterMatch, $is_having) = $this->preBuildArWhere($filterMatch, $arFilterChains, $aliases, $cswFields, $level+1, $is_having);
			}

			$useHaving = max((int) $is_having, (int) $useHaving);

			// define destination of filter: where or having
			if ($level == 0)
			{
				if ($useHaving)
				{
					$havingFilter[$arrayKey] = $filterMatch;
				}
				else
				{
					$whereFilter[$arrayKey] = $filterMatch;
				}
			}
		}

		if ($level == 0)
		{
			$whereFilter['LOGIC'] = $logic;
			$havingFilter['LOGIC'] = $logic;
			return array($whereFilter, $havingFilter);
		}
		else
		{
			return array($arFilter, $useHaving);
		}
	}


	protected function BuildArWhere($arFilterChains, $arFilter, $aliases)
	{
		$sql = array();

		$csw = new CSQLWhere();
		$cswFields = array();

		list($whereFilter, $havingFilter) = $this->preBuildArWhere($arFilter, $arFilterChains, $aliases, $cswFields);

		$csw->SetFields($cswFields);

		$strWhere = $csw->GetQuery($whereFilter);
		$strHaving = $csw->GetQuery($havingFilter);

		$strWhere = str_replace("\n", "\n\t", $strWhere);
		$strHaving = str_replace("\n", "\n\t", $strHaving);


		return array($strWhere, $strHaving);
	}


	protected function BuildGroup($groupChains, $aliases, $selChains, $ordChains)
	{
		$sql = array();

		// user defined группировка
		foreach ($groupChains as $strChain => $groupInfo)
		{
			$groupChain = $groupInfo['value'];
			$lastElem = end($groupChain);

			if (array_key_exists($strChain, $aliases))
			{
				// указано цепочное имя
				//$sql[] = '`' . $aliases[$strChain] . '`';
				$sql[] = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
			}
			elseif (in_array($strChain, $aliases))
			{
				// указан алиас
				//$sql[] = '`' . $strChain . '`';
				$sql[] = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
			}
			else
			{
				// указанное поле засветилось первый раз - его нет в select
				$sql[] = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
			}
		}

		// склеивание дополнительных цепочек
		$addChains = array();

		foreach (array('selChains', 'ordChains') as $argName)
		{
			foreach ($$argName as $key => &$argChain)
			{
				$addChains[$key] = &$argChain;
			}
		}

		foreach ($addChains as $strChain => $chainInfo)
		{

			$chain = $chainInfo['value'];
			$lastElem = end($chain);

			if ($lastElem['value'] instanceof CExprEntityField && $lastElem['value']->IsAggregated())
			{
				// аггрегированные нам тут не нужны
				continue;
			}

			if ($lastElem['value'] instanceof CExprEntityField)
			{
				// we need only last field from buildFrom
				$bfChains = array();
				self::CollectExprChains($lastElem['value'], $bfChains);


				foreach ($bfChains as $bfChainInfo)
				{
					$bfLastElem = end($bfChainInfo['value']);
					$sql[] = $bfLastElem['value']->GetSQLDefinition($bfLastElem['tAlias']);
				}
			}
			else
			{
				if (array_key_exists($strChain, $aliases))
				{
					// указано цепочное имя
					//$sql[] = '`' . $aliases[$strChain] . '`';
					$sql[] = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
				}
				elseif (in_array($strChain, $aliases))
				{
					// указан алиас
					//$sql[] = '`' . $strChain . '`';
					$sql[] = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
				}
				else
				{
	//				if ($lastElem['value'] instanceof CExprEntityField)
	//				{
	//					$sql[] = '`' . $lastElem['value']->GetName() . '`';
	//				}
	//				else
	//				{
	//					// указанное поле засветилось первый раз - его нет в select
	//					$sql[] = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
	//				}
					$sql[] = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']);
				}
			}

		}

		$sql = array_unique($sql);

		return join(",\n\t", $sql);
	}


	protected function BuildOrder($ordChains, $aliases)
	{
		$sql = array();

		foreach ($ordChains as $strChain => $ordInfo)
		{
			if (array_key_exists($strChain, $aliases))
			{
				// указано цепочное имя
				$sql[] = self::$lEsc . $aliases[$strChain] . self::$rEsc . ' '. $ordInfo['sort'];
			}
			elseif (in_array($strChain, $aliases))
			{
				// указан алиас
				$sql[] = self::$lEsc . $strChain . self::$rEsc . ' '. $ordInfo['sort'];
			}
			else
			{
				// указанное поле засветилось первый раз - его нет в select
				$ordChain = $ordInfo['value'];
				$lastElem = end($ordChain);

				if ($lastElem['value'] instanceof CExprEntityField)
				{
					$sql[] = $lastElem['value']->GetSQLDefinition(false) . ' '. $ordInfo['sort'];
				}
				else
				{
					$sql[] = $lastElem['value']->GetSQLDefinition($lastElem['tAlias']) . ' '. $ordInfo['sort'];
				}
			}
		}

		return join(', ', $sql);
	}


	protected function BuildLimit($limit)
	{
		return join(', ', $limit);
	}


	// получение информации о ссылках на другие сущности
	public function GetReferencesCountTo($refEntityName)
	{
		if (array_key_exists($key = strtolower($refEntityName), $this->references))
		{
			return count($this->references[$key]);
		}

		return 0;
	}


	public function GetReferencesTo($refEntityName)
	{
		if (array_key_exists($key = strtolower($refEntityName), $this->references))
		{
			return $this->references[$key];
		}

		return array();
	}


	// getters
	public function GetFields()
	{
		return $this->fields;
	}


	public function GetField($name)
	{
		if ($this->HasField($name))
		{
			return $this->fields[$name];
		}

		throw new Exception(sprintf(
			'%s Entity has no `%s` field.', $this->GetName(), $name
		));
	}


	public function HasField($name)
	{
		return array_key_exists($name, $this->fields);
	}


	public function getUField($name)
	{
		if ($this->hasUField($name))
		{
			return $this->u_fields[$name];
		}

		throw new Exception(sprintf(
			'%s Entity has no `%s` userfield.', $this->GetName(), $name
		));
	}


	public function hasUField($name)
	{
		if (is_null($this->u_fields))
		{
			global $USER_FIELD_MANAGER;

			foreach ($USER_FIELD_MANAGER->GetUserFields($this->uf_id) as $info)
			{
				$this->u_fields[$info['FIELD_NAME']] = new CUEntityField($info, $this);

				// add references for ufield (UF_DEPARTMENT_BY)
				if ($info['USER_TYPE_ID'] == 'iblock_section')
				{
					$info['FIELD_NAME'] .= '_BY';
					$this->u_fields[$info['FIELD_NAME']] = new CUEntityField($info, $this);
				}
			}
		}

		return array_key_exists($name, $this->u_fields);
	}


	public function GetName()
	{
		return $this->name;
	}


	public function GetFilePath()
	{
		return $this->filePath;
	}


	public function GetAliases()
	{
		return $this->tmp_aliases;
	}


	public function GetDBTableName()
	{
		return $this->dbTableName;
	}


	public function IsUts()
	{
		return false;
	}


	public function IsUtm()
	{
		return false;
	}


	public static function IsExists($name)
	{
		return class_exists('C' . $name . 'Entity');
	}


	public static function camel2snake($str)
	{
		return strtolower(preg_replace('/(.)([A-Z])(.*?)/', '$1_$2$3', $str));
	}


	public static function snake2camel($str)
	{
		$str = str_replace('_', ' ', strtolower($str));
		return str_replace(' ', '', ucwords($str));
	}


	// рекурсивное коллекционирование составных атрибутов
	protected static function CollectExprChains(CExprEntityField $exprField, array &$chainsCollection)
	{
		$bfChains = &$exprField->GetBuildFromChains();

		foreach ($bfChains as &$bfChain)
		{
			$chainsCollection[] = array('value' => &$bfChain);

			foreach ($bfChain as &$bfChainElem)
			{
				if ($bfChainElem['value'] instanceof CExprEntityField)
				{
					self::CollectExprChains($bfChainElem['value'], $chainsCollection);
				}
			}
		}
	}


	protected static function CollectExprChainsNEW($chain, array &$chainsCollection)
	{
		$lastElem = end($chain);
		$preChain = array_slice($chain, 0, -1);

		$bfChains = &$lastElem['value']->GetBuildFromChains();

		foreach ($bfChains as &$bfChain)
		{
			$tmpChain = $preChain;
			foreach ($bfChain as &$v)
			{
				$tmpChain[] = &$v;
			}

			$chainsCollection[] = array('value' => $tmpChain);

			foreach ($bfChain as &$bfChainElem)
			{
				if ($bfChainElem['value'] instanceof CExprEntityField)
				{
					//$tmpChain = $preChain;
					//$tmpChain[] = $bfChainElem;

					self::CollectExprChainsNEW($tmpChain, $chainsCollection);
				}
			}
		}

	}


	public static function GetObjectChain($initEntity, $strChain)
	{
		// пока не знаю, зачем это
		$selFields = array();
		$selReferences = array();

		// старт обработки
		$objChain = array();
		$fieldChain = explode('.', $strChain);

		$prev_entity = $initEntity;

		$i = 0;

		foreach ($fieldChain as $chainElem)
		{
			$isFirstElem = $i == 0;
			$isLastElem = (++$i == count($fieldChain));

			// все элементы должны быть reference или entity
			// последний элемент может быть обычным полем
			if ($prev_entity->HasField($chainElem))
			{
				// поле найдено у текущей сущности
				$field = $prev_entity->GetField($chainElem);

				if ($field instanceof CReferenceEntityField)
				{
					// нужен join
					$selReferences[] = $field;
					$prev_entity = $field->GetRefEntity();
				}
				elseif ($isLastElem)
				{
					// expr и обычные поля могут быть только последними
					$selFields[] = $field;
				}
				else
				{
					throw new Exception(sprintf(
						'Normal fields can be only the last in chain, `%s` %s is not the last.',
						$field->GetName(), get_class($field)
					));
				}

				$objChain[] = array(
					'tAlias' => '',
					'value' => $field
				);
			}
			elseif ($prev_entity->HasUField($chainElem))
			{
				// extend chain with utm/uts entity
				$ufield = $prev_entity->GetUField($chainElem);

				$u_entity = null;

				if ($ufield->isMultiple())
				{
					// utm table multi-value

					// add utm entity  user.utm:source_object (1:N)
					$utm_entity = $u_entity = CBaseEntity::GetInstance('Utm'.$prev_entity->GetName());
					$objChain[] = array(
						'tAlias' => '',
						'uField' => $ufield,
						'value' => array($utm_entity, $utm_entity->GetField('SOURCE_OBJECT'))
					);

					if ($ufield->getTypeId() == 'iblock_section'
						&& substr($ufield->getName(), -3) == '_BY'
						&& $prev_entity->HasUField(substr($ufield->getName(), 0, -3))
					)
					{
						// connect next entity
						$utm_fname = $ufield->getName();
						$prev_entity = CBaseEntity::GetInstance('IblockSection');
					}
					else
					{
						$utm_fname = $ufield->getValueFieldName();
					}

					$objChain[] = array(
						'tAlias' => '',
						'uField' => $ufield,
						'value' => $utm_entity->GetField($utm_fname)
					);
				}
				else
				{
					// uts table - single value

					// add uts entity user.uts (1:1)
					$uts_entity = $u_entity = CBaseEntity::GetInstance('Uts'.$prev_entity->GetName());
					$objChain[] = array(
						'tAlias' => '',
						'value' => $prev_entity->getField('UTS_OBJECT')
					);

					// add `value` field
					$objChain[] = array(
						'tAlias' => '',
						'value' => $uts_entity->getField($chainElem)
					);
				}
			}
			elseif (CBaseEntity::IsExists($chainElem)
				&& CBaseEntity::GetInstance($chainElem)->GetReferencesCountTo($prev_entity->GetName()) == 1
			)
			{
				// элементом цепи является другая entity с 1 связью к данной
				// нужно выявить ее единственное связующее поле
				$refEntity = CBaseEntity::GetInstance($chainElem);
				$field = end($refEntity->GetReferencesTo($prev_entity->GetName()));

				$selReferences[] = $field;
				$prev_entity = $refEntity;

				$objChain[] = array(
					'tAlias' => '',
					'value' => array($refEntity, $field)
				);
			}
			elseif ( ($posWh = strpos($chainElem, ':')) > 0
					&& CBaseEntity::IsExists(($refEntityName = substr($chainElem, 0, $posWh)))
					&& CBaseEntity::GetInstance($refEntityName)->HasField($refFieldName = substr($chainElem, $posWh+1))
					&& CBaseEntity::GetInstance($refEntityName)->GetField($refFieldName)->getRefEntity()->GetName() == $prev_entity->getName()
			)
			{
				// элементом цепи является другая entity с >1 связями к данной
				// запись вида NewsArticleWhereAuthor, NewsArticleWhereLastCommenter
				// NewsArticle - сущность, Author и LastCommenter - ref поле на prev_entity
				$objChain[] = array(
					'tAlias' => '',
					'value' => array(
						CBaseEntity::GetInstance($refEntityName),
						CBaseEntity::GetInstance($refEntityName)->GetField($refFieldName)
					)
				);

				$prev_entity = CBaseEntity::GetInstance($refEntityName);
			}
			elseif ($chainElem == '*' && $isFirstElem)
			{
				// all fields of init entity
				$objChain[] = array(
					'tAlias' => '',
					'value' => $initEntity
				);
			}
			elseif ($chainElem == '*' && $isLastElem)
			{
				continue;
			}
			else
			{
				// unknown chain
				throw new Exception(sprintf(
					'Unknown field definition `%s` (%s) for %s Entity.',
					$chainElem, $strChain, $prev_entity->GetName()
				), 100);
			}
		}

		return $objChain;
	}


	public static function GetAliasByChain($chain, $prefix = '')
	{
		$alias = array();

		foreach ($chain as $chainElem)
		{
			if ($chainElem['value'] instanceof CReferenceEntityField)
			{
				if ($chainElem['value']->GetRefEntity()->isUts())
				{
					continue;
				}

				$alias[] = $chainElem['value']->GetName();
			}
			elseif (is_array ($chainElem['value'])
				&& $chainElem['value'][0] instanceof CBaseEntity
				&& $chainElem['value'][1] instanceof CReferenceEntityField
			)
			{
				if ($chainElem['value'][0]->isUtm())
				{
					continue;
				}

				$alias[] = strtoupper(self::camel2snake($chainElem['value'][0]->GetName())) . '_' . $chainElem['value'][1]->GetName();
			}
			else
			{
				if (!empty($chainElem['uField']))
				{
					$alias[] = $chainElem['uField']->GetName();
				}
				else
				{
					$alias[] = $chainElem['value']->GetName();
				}
			}
		}

		$strAlias = join('_', $alias);

		if (count($chain) > 1 && !empty($prefix))
		{
			// doesn't apply for fields of initial entity
			$strAlias = $prefix.'_'.$strAlias;
		}

		return $strAlias;
	}


	public static function GetAliasByDefinition($str, $prefix = '')
	{
		$alias = array();
		$cnt = 0;

		foreach (explode('.', $str) as $elem)
		{
			if ($elem == strtoupper($elem))
			{
				$alias[] = $elem;
			}
			else
			{
				$tmp = explode(':', $elem);
				$alias[] = strtoupper(self::camel2snake($tmp[0]));
				if (!empty($tmp[1]))
				{
					$alias[] = $tmp[1];
				}
			}

			$cnt++;
		}

		$strAlias = join('_', $alias);

		if ($cnt > 1 && !empty($prefix))
		{
			// doesn't apply for fields of initial entity
			$strAlias = $prefix.'_'.$strAlias;
		}

		return $strAlias;
	}


	public static function optimizeAliases($query)
	{
		$replaced = array();

		preg_match_all(
			'/\sAS\s+'.preg_quote(self::$lEsc).'([a-z0-9_]+)'.preg_quote(self::$rEsc).'/i',
			$query, $matches
		);

		if (!empty($matches[1]))
		{
			// search for long aliases
			foreach ($matches[1] as $alias)
			{
				if (strlen($alias) > 30)
				{
					$newAlias = 'ALIAS_'.count($replaced);
					$replaced[$newAlias] = $alias;
				}
			}

			// replace aliases in query
			if (!empty($replaced))
			{
				foreach ($replaced as $newAlias => $alias)
				{
					$query = str_replace(
						self::$lEsc.$alias.self::$rEsc,
						self::$lEsc.$newAlias.self::$rEsc. '/* '.$alias.' */',
						$query
					);
				}
			}
		}

		return array($query, $replaced);
	}

}



