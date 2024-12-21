<?
function createField($entityId, $fieldName, $fieldType = 'string') {
	$arUserField = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => $entityId, "FIELD_NAME" => $fieldName))->Fetch();
	if(!$arUserField)
	{
		$arFields = array(
			"FIELD_NAME" => $fieldName,
			"ENTITY_ID" => $entityId,
			"USER_TYPE_ID" => $fieldType,
			"XML_ID" => $fieldName,
			"SORT" => 100,
			"MULTIPLE" => "N",
			"MANDATORY" => "N",
			"SHOW_FILTER" => "I",
			"SHOW_IN_LIST" => "Y",
			"EDIT_IN_LIST" => "Y",
			"IS_SEARCHABLE" => "N",
		);
		$ob = new CUserTypeEntity();
		$FIELD_ID = $ob->Add($arFields);
		return $FIELD_ID;
	} else {
		return false;
	}
}

function wordform($num, $wordForms){
	$num = abs($num) % 100;
	$num_x = $num % 10;
	if ($num > 10 && $num < 20)
		return $wordForms[2];
	if ($num_x > 1 && $num_x < 5)
		return $wordForms[1];
	if ($num_x == 1)
		return $wordForms[0];
	return $wordForms[2];
}
?>