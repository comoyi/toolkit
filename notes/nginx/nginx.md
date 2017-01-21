#### 测试配置是否有语法错误
```
nginx -t
```

#### 重新加载配置
```
nginx -s reload
```

---

```
server {
    listen       80;
    server_name  skyline.ninja;
    access_log      /var/log/nginx/access.log ;
    if ( $host ~* ^skyline\.ninja$  ) {
        set $project www;
    }
    root  /home/html/$project/www;
    index index.php index.html;

    if (-f $request_filename) {
        break;
    }
    
    location ~ \.php$ {
        root  /home/html/$project/www;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
    
    error_page 404 /custom_404.html;
    location = /custom_404.html {
        root /home/html/error_page;
        internal;
    }
}

server {
    listen       80;
    server_name  *.skyline.ninja;
    access_log      /var/log/nginx/access.log ;
    if ( $host ~* ^([\w-]+)\.skyline\.ninja$  ) {
        set $project $1;
    }
    root  /home/html/$project/www;
    index index.php index.html;

    if (-f $request_filename) {
        break;
    }
    
    location ~ \.php$ {
        root  /home/html/$project/www;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
    
    error_page 404 /custom_404.html;
    location = /custom_404.html {
        root /home/html/error_page;
        internal;
    }
}
```
