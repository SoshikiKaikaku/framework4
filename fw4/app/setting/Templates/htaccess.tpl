
#Header set Cache-Control "no-cache, must-revalidate"
#Header set Pragma "no-cache"

RewriteEngine on
RewriteBase /

RewriteCond %{REQUEST_URI} ^{$subpath}/(.*).html$ [NC]
RewriteRule ^(.*).html$ {$subpath}/fw4/app.php?class=$1&function=page [L]

RewriteCond %{REQUEST_URI} ^{$subpath}/images/(.*)$ [NC]
RewriteRule ^/images/(.*)$ {$subpath}/classes/app/public/images/$1 [L]

RewriteCond %{REQUEST_URI} ^{$subpath}/(.*)\*(.*)$ [NC]
RewriteRule ^(.*)\*(.*)$ {$subpath}/fw4/app.php?class=$1&function=$2 [L]

RewriteCond %{REQUEST_URI} ^{$subpath}/app/(.*)$ [NC]
RewriteRule ^app/(.*)$ {$subpath}/fw4/$1 [L]

RewriteCond %{REQUEST_URI} ^{$subpath}/$
RewriteRule ^$ {$subpath}/app.php?class={$class}&function={$function} [R,L]

RewriteCond %{REQUEST_URI} ^{$subpath}/robots.txt$
RewriteRule ^(.*)$ {$subpath}/robots.txt [L]

RewriteCond %{REQUEST_URI} !^{$subpath}/fw4/(.*)$ [NC]
RewriteRule ^(.*)$ {$subpath}/fw4/$1 [L]
