# 使用 WordPress 官方镜像
FROM wordpress:latest

WORKDIR /var/www/html

# 安装必要工具
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# 📦 复制自定义主题到容器中
COPY --chown=www-data:www-data ./wp-content/themes/hello-elementor /var/www/html/wp-content/themes/hello-elementor
<<<<<<< HEAD
=======
# 仅复制 WooCommerce、Elementor 插件与媒体图片
COPY --chown=www-data:www-data ./wp-content/plugins/woocommerce /var/www/html/wp-content/plugins/woocommerce
COPY --chown=www-data:www-data ./wp-content/plugins/elementor /var/www/html/wp-content/plugins/elementor
COPY --chown=www-data:www-data ./wp-content/uploads /var/www/html/wp-content/uploads
>>>>>>> 096673c0 (upload plungin and pics)

# 设置正确的权限
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/hello-elementor

# 创建统一的启动脚本（包含端口配置和 MySQL 等待）
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "=== WordPress Starting ==="\n\
\n\
# 配置 Apache 端口（Railway 动态端口支持）\n\
if [ -n "$PORT" ]; then\n\
  echo "Configuring Apache to listen on Railway port: $PORT"\n\
  sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\n\
  sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
else\n\
  echo "Using default port 80"\n\
fi\n\
\n\
# 显示数据库环境变量\n\
echo "DB Host: ${WORDPRESS_DB_HOST}"\n\
echo "DB User: ${WORDPRESS_DB_USER}"\n\
echo "DB Name: ${WORDPRESS_DB_NAME}"\n\
\n\
# 解析 host 和 port\n\
DB_HOST=$(echo $WORDPRESS_DB_HOST | cut -d: -f1)\n\
DB_PORT=$(echo $WORDPRESS_DB_HOST | cut -d: -f2)\n\
if [ "$DB_PORT" == "$DB_HOST" ]; then\n\
  DB_PORT=3306\n\
fi\n\
\n\
echo "Parsed - Host: $DB_HOST, Port: $DB_PORT"\n\
\n\
# 等待 MySQL 端口可用\n\
echo "Waiting for MySQL on $DB_HOST:$DB_PORT..."\n\
TIMEOUT=180\n\
ELAPSED=0\n\
until nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do\n\
  if [ $ELAPSED -ge $TIMEOUT ]; then\n\
    echo "ERROR: MySQL connection timeout after ${TIMEOUT}s"\n\
    echo "Attempting to continue anyway..."\n\
    break\n\
  fi\n\
  echo "MySQL not ready yet... waiting ($ELAPSED/${TIMEOUT}s)"\n\
  sleep 5\n\
  ELAPSED=$((ELAPSED + 5))\n\
done\n\
\n\
if nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; then\n\
  echo "✓ MySQL connection confirmed!"\n\
else\n\
  echo "⚠ Warning: Could not confirm MySQL connection, but continuing..."\n\
fi\n\
\n\
echo "✓ Starting WordPress..."\n\
\n\
# 启动 Apache 和 WordPress\n\
exec docker-entrypoint.sh apache2-foreground' > /usr/local/bin/start-wordpress.sh \
    && chmod +x /usr/local/bin/start-wordpress.sh

# 暴露端口
EXPOSE 80

# 使用 ENTRYPOINT 确保启动脚本被执行
ENTRYPOINT ["/usr/local/bin/start-wordpress.sh"]