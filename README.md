
nginx에 아래 내용을 추가

location ~ \.php$ {
                try_files $uri /index.php =404;
                fastcgi_pass unix:/var/run/php-fpm/www.sock;
                fastcgi_read_timeout 1800;
                fastcgi_index index.php;
                fastcgi_split_path_info ^(.+\.php)(/.+)$;
                fastcgi_param SCRIPT_FILENAME $request_filename;
                include fastcgi_params;
        }

