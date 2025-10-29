# WordPress Docker 镜像用于 Render 部署
FROM wordpress:latest

# 设置工作目录
WORKDIR /var/www/html

# 安装必要的工具
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# 复制自定义主题
COPY --chown=www-data:www-data ./wp-content/themes/hello-elementor /var/www/html/wp-content/themes/hello-elementor

# 设置正确的权限
RUN chown -R www-data:www-data /var/www/html/wp-content/themes/hello-elementor

# 暴露端口 80
EXPOSE 80

# 启动 Apache
CMD ["apache2-foreground"]

