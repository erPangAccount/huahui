/**
 * @apiDefine Page
 * @apiParam    {Integer}    [page=1]    页码
 * @apiParam    {Integer}    [pageSize=10]    每页数据量
 */

/**
 * @apiDefine Sort
 * @apiParam    {String}    [sort_field=created_at]    排序字段
 * @apiParam    {Integer}    [sort_order=asc]    排序方式
 */


/**
 * @apiDefine Error
 * @apiErrorExample Error:
 *     {
 *         "status": error code, //405 request method error; 0 success; other number => other error
 *         "message": "error msg",
 *         "data": null
 *     }
 */
