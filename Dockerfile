# WordPress Docker 镜像用于 Railway 部署
FROM wordpress:latest

# 设置工作目录
WORKDIR /var/www/html

# 安装必要的工具
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# 复制自定义主题
COPY --chown=www-data:www-data ./wp-content/themes/hello-elementor /var/www/html/wp-content/themes/hello-elementor

# 设置正确的权限
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/hello-elementor

# 创建简化的启动脚本（只等待 MySQL）
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "=== WordPress Starting ==="\n\
\n\
# 显示数据库环境变量\n\
echo "DB Host: ${WORDPRESS_DB_HOST}"\n\
echo "DB User: ${WORDPRESS_DB_USER}"\n\
echo "DB Name: ${WORDPRESS_DB_NAME}"\n\
\n\
# 解析主机和端口\n\
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
TIMEOUT=300\n\
ELAPSED=0\n\
until nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do\n\
  if [ $ELAPSED -ge $TIMEOUT ]; then\n\
    echo "ERROR: MySQL connection timeout after ${TIMEOUT}s"\n\
    exit 1\n\
  fi\n\
  echo "MySQL not ready yet... waiting ($ELAPSED/${TIMEOUT}s)"\n\
  sleep 5\n\
  ELAPSED=$((ELAPSED + 5))\n\
done\n\
\n\
echo "✓ MySQL port is open!"\n\
echo "✓ Starting WordPress..."\n\
\n\
# 启动 Apache 和 WordPress（使用默认端口 80）\n\
exec docker-entrypoint.sh apache2-foreground' > /usr/local/bin/start-wordpress.sh \
    && chmod +x /usr/local/bin/start-wordpress.sh

# 暴露端口 80（Railway 会自动处理端口映射）
EXPOSE 80

# 使用启动脚本
CMD ["/usr/local/bin/start-wordpress.sh"]
