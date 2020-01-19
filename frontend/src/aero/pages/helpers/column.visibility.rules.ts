import { BaseHelper } from './base';

function getFormElementVisibilityBaseKey(elementId)
{
    var formElementVisibilityBaseKey = "formElementVisibility.";

    if(elementId.indexOf("ife-") > -1)
        formElementVisibilityBaseKey += elementId.split("ng-reflect-id=\"")[1].split('"')[0];

    return formElementVisibilityBaseKey + ".";
}

function columnDbTypeIdTrigger(tableName, columnName, elementId, data)
{
    var geoColumnIds= [11, 12, 13, 14, 15, 16];

    var formElementVisibilityBaseKey = getFormElementVisibilityBaseKey(elementId);

    var visible = geoColumnIds.includes(parseInt(data[columnName]));
    
    BaseHelper.writeToPipe(formElementVisibilityBaseKey+"srid", visible);
}

function joinTableIdsTrigger(tableName, columnName, elementId, data)
{
    var visible = false;
    if(typeof data[columnName] != "undefined" && data[columnName] != "" && data[columnName] != "[]")
        visible = true;
    
    var formElementVisibilityBaseKey = getFormElementVisibilityBaseKey(elementId);
    BaseHelper.writeToPipe(formElementVisibilityBaseKey+"join_columns", visible);
}



export const columnVisibilityRules = 
{
    "column_db_type_id": columnDbTypeIdTrigger,
    "join_table_ids": joinTableIdsTrigger
};