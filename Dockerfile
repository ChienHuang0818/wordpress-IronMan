# WordPress Docker 镜像用于 Railway 部署
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

# 使用 WordPress 官方镜像的默认启动方式
# 不使用自定义脚本，让 WordPress 自己处理所有事情
