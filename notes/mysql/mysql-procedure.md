#### 根据已存在的表创建分表
```
BEGIN

    DECLARE num_count int default 0;
    DECLARE sub_num int default 10;
    DECLARE sub_suffix_separator varchar(50) default '_';
    DECLARE origin_table_name varchar(100);
    DECLARE sub_table_name varchar(100);

    -- create table
    SET num_count = 0;
    SET sub_num = 10;
    SET sub_suffix_separator = '_';
    SET origin_table_name = 'table_name_user';
    WHILE num_count < sub_num DO
        SET sub_table_name = concat(origin_table_name, sub_suffix_separator, num_count);
        SET @sql_str = concat('CREATE TABLE IF NOT EXISTS ', sub_table_name, ' LIKE ', origin_table_name);
        PREPARE sql_str FROM @sql_str;
        EXECUTE sql_str;
        DEALLOCATE PREPARE sql_str;
        SET num_count = num_count+1;
    END WHILE;

    -- create table
    SET num_count = 0;
    SET sub_num = 10;
    SET sub_suffix_separator = '_';
    SET origin_table_name = 'table_name_statistics';
    WHILE num_count < sub_num DO
        SET sub_table_name = concat(origin_table_name, sub_suffix_separator, num_count);
        SET @sql_str = concat('CREATE TABLE IF NOT EXISTS ', sub_table_name, ' LIKE ', origin_table_name);
        PREPARE sql_str FROM @sql_str;
        EXECUTE sql_str;
        DEALLOCATE PREPARE sql_str;
        SET num_count = num_count+1;
    END WHILE;

END
```
