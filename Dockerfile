# Use official WordPress image
FROM wordpress:php8.2-apache

WORKDIR /var/www/html

# Install necessary tools
RUN apt-get update && apt-get install -y \
    unzip \
    curl \
    netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

# Copy WooCommerce, Elementor plugins and custom theme (if pushed to Git)
# COPY --chown=www-data:www-data ./wp-content/plugins/woocommerce /var/www/html/wp-content/plugins/woocommerce
# COPY --chown=www-data:www-data ./wp-content/plugins/elementor /var/www/html/wp-content/plugins/elementor
COPY --chown=www-data:www-data ./wp-content/themes/hello-elementor /var/www/html/wp-content/themes/hello-elementor

# Health check: Railway will automatically restart unhealthy containers based on this
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
  CMD curl -fsS http://localhost/wp-json/ || exit 1

# Create unified startup script (includes port configuration and MySQL wait)
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "=== WordPress Starting ==="\n\
\n\
# Configure Apache port (Railway dynamic port support)\n\
if [ -n "$PORT" ]; then\n\
  echo "Configuring Apache to listen on Railway port: $PORT"\n\
  sed -i "s/^Listen .*/Listen $PORT/" /etc/apache2/ports.conf\n\
  sed -i "s/<VirtualHost \*:.*>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf\n\
else\n\
  echo "Using default port 80"\n\
fi\n\
\n\
# Display database environment variables\n\
echo "DB Host: ${WORDPRESS_DB_HOST}"\n\
echo "DB User: ${WORDPRESS_DB_USER}"\n\
echo "DB Name: ${WORDPRESS_DB_NAME}"\n\
\n\
# Parse host and port\n\
DB_HOST=$(echo $WORDPRESS_DB_HOST | cut -d: -f1)\n\
DB_PORT=$(echo $WORDPRESS_DB_HOST | cut -d: -f2)\n\
if [ "$DB_PORT" == "$DB_HOST" ]; then\n\
  DB_PORT=3306\n\
fi\n\
echo "Parsed - Host: $DB_HOST, Port: $DB_PORT"\n\
\n\
# Prepare uploads directory and permissions (Volume mount permissions often get overwritten, need to handle on startup)\n\
mkdir -p /var/www/html/wp-content/uploads\n\
chown -R www-data:www-data /var/www/html/wp-content/uploads /var/www/html/wp-content/plugins /var/www/html/wp-content/themes\n\
chmod -R u+rwX,go-rwx /var/www/html/wp-content/uploads /var/www/html/wp-content/plugins /var/www/html/wp-content/themes\n\
\n\
# Wait for MySQL port to be available\n\
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
# Start Apache and WordPress\n\
exec docker-entrypoint.sh apache2-foreground' > /usr/local/bin/start-wordpress.sh \
    && chmod +x /usr/local/bin/start-wordpress.sh

# Expose port
EXPOSE 80

# Use ENTRYPOINT to ensure startup script is executed
ENTRYPOINT ["/usr/local/bin/start-wordpress.sh"]

 