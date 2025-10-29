# WordPress Docker 镜像用于 Railway 部署
FROM wordpress:latest

# 设置工作目录
WORKDIR /var/www/html

# 安装必要的工具和 MySQL 客户端
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# 复制自定义主题
COPY --chown=www-data:www-data ./wp-content/themes/hello-elementor /var/www/html/wp-content/themes/hello-elementor

# 设置正确的权限
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/hello-elementor

# Railway 使用 PORT 环境变量，但 WordPress 默认监听 80
# 创建启动脚本来处理端口
RUN echo '#!/bin/bash\n\
# 使用 Railway 的 PORT 环境变量，如果未设置则默认 80\n\
export APACHE_PORT=${PORT:-80}\n\
sed -i "s/Listen 80/Listen $APACHE_PORT/g" /etc/apache2/ports.conf\n\
sed -i "s/:80/:$APACHE_PORT/g" /etc/apache2/sites-available/000-default.conf\n\
\n\
# 等待 MySQL 可用\n\
echo "Waiting for MySQL..."\n\
until mysql -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" -e "SELECT 1" >/dev/null 2>&1; do\n\
  echo "MySQL is unavailable - sleeping"\n\
  sleep 2\n\
done\n\
echo "MySQL is up - starting WordPress"\n\
\n\
# 启动 Apache\n\
exec apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# 暴露端口（Railway 会动态分配）
EXPOSE ${PORT:-80}

# 使用自定义启动脚本
CMD ["/usr/local/bin/start.sh"]

