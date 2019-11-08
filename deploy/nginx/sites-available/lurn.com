# www to non-www redirect -- duplicate content is BAD:
# https://github.com/h5bp/html5-boilerplate/blob/5370479476dceae7cc3ea105946536d6bc0ee468/.htaccess#L362
# Choose between www and non-www, listen on the *wrong* one and redirect to
# the right one -- http://wiki.nginx.org/Pitfalls#Server_Name

server {
  # don't forget to tell on which port this server listens
  listen [::]:80;
  listen 80;

  # listen on the www host
  server_name www.lurn.com;

  # and redirect to the non-www host (declared below)
  return 301 $scheme://lurn.com$request_uri;
}

server {
  # listen [::]:80 accept_filter=httpready; # for FreeBSD
  # listen 80 accept_filter=httpready; # for FreeBSD
  # listen [::]:80 deferred; # for Linux
  # listen 80 deferred; # for Linux
  listen [::]:80;
  listen 80;

  # The host name to respond to
  server_name lurn.com lurncentral.lurntechnology.com LurnNation-1953728571.us-east-1.elb.amazonaws.com;

  # Path for static files
  root /var/www/lurn.com/live/public;
  index index.php index.html;

  # Specify a charset
  charset utf-8;

  # Custom 404 page
  error_page 404 /404.html;

  # Include the basic h5bp config set
  include h5bp/basic.conf;
  include lurn-specific.conf;

  location / {
      try_files $uri $uri/ =404 /index.php?$query_string;
  }

  location ~* \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(.*)$;
        fastcgi_pass unix:/run/php/php7.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_buffers 35 32k;
        include fastcgi_params;
        fastcgi_param REMOTE_ADDR $http_x_forwarded_for;
        fastcgi_read_timeout 300;
  }

    location /blog {
        try_files $uri $uri/ /blog/index.php?$query_string;

        location ~ \.php$|^/blog/update.php {
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

            fastcgi_intercept_errors off;
            fastcgi_buffer_size 16k;
            fastcgi_buffers 4 16k;
            fastcgi_connect_timeout 300;
            fastcgi_send_timeout 300;
            fastcgi_read_timeout 300;
        }
    }

    location @rewrite {
        rewrite ^/(.*)$ /blog/index.php?q=$1;
    }

    location ~ ^(/[a-z\-]+)?/system/files/ {
        try_files $uri /blog/index.php?$query_string;
    }

    location ~ ^/blog/sites/.*/files/styles {
        access_log      off;
        expires         max;
        try_files $uri @rewrite;
    }

    location ~* ^/(s3fs-css|s3fs-js)/(.*) {
      set $s3_base_path 'lurn-nation-static.s3.amazonaws.com/blog/files';
      set $file_path $2;

      resolver 172.16.0.23 valid=300s;
      resolver_timeout 10s;

      proxy_pass http://$s3_base_path/$file_path;
    }

    location ~* \.(eot|ttf|woff|woff2)$ {
        add_header Access-Control-Allow-Origin *;
    }

    location /remote-form {
        add_header X-Frame-Options SAMEORIGIN;

        location ~ \.php$ {
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

            fastcgi_intercept_errors off;
            fastcgi_buffer_size 16k;
            fastcgi_buffers 4 16k;
            fastcgi_connect_timeout 300;
            fastcgi_send_timeout 300;
            fastcgi_read_timeout 300;
        }
    }
}
