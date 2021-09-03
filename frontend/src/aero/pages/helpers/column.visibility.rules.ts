import { BaseHelper } from './base';




/****    Common Functions     *****/

function getFormElementVisibilityBaseKey(elementId)
{
    var formElementVisibilityBaseKey = "formElementVisibility.";

    if(elementId.indexOf("ife-") > -1)
    {
        //formElementVisibilityBaseKey += elementId.split("ng-reflect-id=\"")[1].split('"')[0];
        formElementVisibilityBaseKey += "ife-"+elementId.split("#ife-")[1].split('inFormModal')[0];
    }

    return formElementVisibilityBaseKey + ".";
}

function setElementVisibility(elementId, column, visible)
{
    var formElementVisibilityBaseKey = getFormElementVisibilityBaseKey(elementId);
    BaseHelper.writeToPipe(formElementVisibilityBaseKey+column, visible);
}

function elementIsNull(data, columnName)
{
    if(typeof data[columnName] == "undefined") return true;
    if(data[columnName] == null) return true;
    if(data[columnName] == "") return true;
    if(data[columnName] == "[]") return true;

    return false;
}


/****    Trigger Rules Functions     ****/

function columnDbTypeIdTrigger(tableName, columnName, elementId, data)
{
    var geoColumnIds= [11, 12, 13, 14, 15, 16];
    var visible = geoColumnIds.includes(parseInt(data[columnName]));
    setElementVisibility(elementId, "srid", visible);
}

function joinTableIdsTrigger(tableName, columnName, elementId, data)
{
    var visible = elementIsNull(data, columnName);
    setElementVisibility(elementId, "join_columns", !visible);
}

function relationTableIdTrigger(tableName, columnName, elementId, data)
{
    var visible = elementIsNull(data, columnName);
    
    setElementVisibility(elementId, "relation_sql", visible);
    setElementVisibility(elementId, "column_data_source_id", visible);
}

function relationSqlTrigger(tableName, columnName, elementId, data)
{
    var visible = elementIsNull(data, columnName);
    
    setElementVisibility(elementId, "relation_table_id", visible);
    setElementVisibility(elementId, "column_data_source_id", visible);
    setElementVisibility(elementId, "relation_source_column_id", visible);
    setElementVisibility(elementId, "relation_display_column_id", visible);
    setElementVisibility(elementId, "join_table_ids", visible);
}

function relationSourceColumnIdTrigger(tableName, columnName, elementId, data)
{
    var visible = elementIsNull(data, columnName);    
    setElementVisibility(elementId, "relation_source_column", visible);
}

function relationDisplayColumnIdTrigger(tableName, columnName, elementId, data)
{
    var visible = elementIsNull(data, columnName);    
    setElementVisibility(elementId, "relation_display_column", visible);
}

function relationSourceColumnTrigger(tableName, columnName, elementId, data)
{
    var visible = elementIsNull(data, columnName);    
    setElementVisibility(elementId, "relation_source_column_id", visible);
}

function relationDisplayColumnTrigger(tableName, columnName, elementId, data)
{
    var visible = elementIsNull(data, columnName);    
    setElementVisibility(elementId, "relation_display_column_id", visible);
}

function columnDataSourceIdTrigger(tableName, columnName, elementId, data)
{
    if(!elementIsNull(data, 'relation_table_id')) return;
    if(!elementIsNull(data, 'relation_sql')) return;
    
    var visible = elementIsNull(data, columnName); 
       
    setElementVisibility(elementId, "relation_table_id", visible);
    setElementVisibility(elementId, "relation_source_column_id", visible);
    setElementVisibility(elementId, "relation_display_column_id", visible);
    setElementVisibility(elementId, "relation_source_column", visible);
    setElementVisibility(elementId, "relation_display_column", visible);
    setElementVisibility(elementId, "join_table_ids", visible);
    setElementVisibility(elementId, "relation_sql", visible);
}

function customLayerTypeIdTrigger(tableName, columnName, elementId, data)
{
    var visible = false;
    if(data[columnName] == "2") visible = true;//wfs
    
    setElementVisibility(elementId, "period", visible);
    
    if(tableName == "external_layers")
        setElementVisibility(elementId, "layer_style_id", visible);
    else
        setElementVisibility(elementId, "layer_style_id", true);
}

function dataSourceTypeIdTrigger(tableName, columnName, elementId, data)
{
    var visible = true;
    if(data[columnName] == "3") visible = false;//excel
    
    setElementVisibility(elementId, "host", visible);
    setElementVisibility(elementId, "user_name", visible);
    setElementVisibility(elementId, "passw", visible);
}

export const columnVisibilityRules = 
{
    "column_db_type_id": columnDbTypeIdTrigger,
    "join_table_ids": joinTableIdsTrigger,

    "relation_table_id": relationTableIdTrigger,
    "relation_sql": relationSqlTrigger,
    "relation_source_column_id": relationSourceColumnIdTrigger,
    "relation_display_column_id": relationDisplayColumnIdTrigger,
    "relation_source_column": relationSourceColumnTrigger,
    "relation_display_column": relationDisplayColumnTrigger,
    "column_data_source_id": columnDataSourceIdTrigger,
    "custom_layer_type_id": customLayerTypeIdTrigger,
    "data_source_type_id": dataSourceTypeIdTrigger
};