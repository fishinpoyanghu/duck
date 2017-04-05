<?php
return array(
    //'配置项'=>'配置值'
    'DB_TYPE'=>'mysql',
    'DB_HOST'=>'192.168.0.2',
    'DB_USER'=>'17gou',
    'DB_PWD'=>'nfjkernksdferniui',// array('192.168.0.2', 'agent', 'nfjkernksdferniui'),
    'DB_NAME'=>'wangluobi',
    'DB_PORT'=>'3306',//
    'DB_PREFIX'=>'gs_',
    'DB_BACKUP'=>'./db',
    'LOAD_EXT_CONFIG'=>'system',
    'TMPL_L_DELIM'=>'<{',
    'TMPL_R_DELIM'=>'}>',
    //模版的配置
    'TMPL_PARSE_STRING' =>  array( // 添加输出替换
        '__LIB__'    =>  __ROOT__.'/Public/resource',
        '__HUI__'    =>  __ROOT__.'/Public/static/h-ui',
        '__BLACK__'    =>  __ROOT__.'/Public/black',
        '__TREE__'    =>  __ROOT__.'/Public/tree',

    ),
    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       =>  3600,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_PREFIX'     =>  'gscache_',     // 缓存前缀
    'DATA_CACHE_TYPE'       =>  'File',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_PATH'       =>  TEMP_PATH.'gs/',// 缓存路径设置 (仅对File方式缓存有效)
    'DEFAULT_MODULE'=>'Api',
);
