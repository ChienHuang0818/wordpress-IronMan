# 使用 WordPress 官方镜像
FROM wordpress:latest

# 创建简单的启动脚本来配置端口
RUN echo '#!/bin/bash\n\
# 如果Railway提供了PORT环境变量，配置Apache监听该端口\n\
if [ -n "$PORT" ]; then\n\
  echo "Configuring Apache to listen on port $PORT"\n\
  sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\n\
  sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf\n\
fi\n\
# 使用WordPress官方的entrypoint\n\
exec docker-entrypoint.sh apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# 暴露端口80（Railway会动态映射）
EXPOSE 80

# 使用启动脚本
CMD ["/usr/local/bin/start.sh"]
