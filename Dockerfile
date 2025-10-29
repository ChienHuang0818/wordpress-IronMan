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

# 创建增强的启动脚本
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "=== WordPress Starting ==="\n\
\n\
# 显示环境变量（不显示密码）\n\
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
# 等待 MySQL 端口可用（使用 netcat）\n\
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
echo "Starting Apache and WordPress..."\n\
\n\
# 启动 Apache（使用 WordPress 官方镜像的默认命令）\n\
exec docker-entrypoint.sh apache2-foreground' > /usr/local/bin/custom-start.sh \
    && chmod +x /usr/local/bin/custom-start.sh

# 创建端口配置脚本
RUN echo '#!/bin/bash\n\
# Railway 提供 PORT 环境变量\n\
if [ -n "$PORT" ]; then\n\
  echo "Configuring Apache to listen on port $PORT"\n\
  sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\n\
  sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
fi\n\
exec /usr/local/bin/custom-start.sh' > /usr/local/bin/configure-port.sh \
    && chmod +x /usr/local/bin/configure-port.sh

# 暴露端口
EXPOSE 80

# 使用端口配置脚本
CMD ["/usr/local/bin/configure-port.sh"]

