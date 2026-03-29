#!/bin/bash

# Fix permissions for Laravel on macOS (Apache _www user)

PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo "🔧 Fixing Laravel permissions in: $PROJECT_ROOT"

# Make sure bootstrap/cache and storage exist
mkdir -p "$PROJECT_ROOT/storage" "$PROJECT_ROOT/bootstrap/cache"

# Reset ownership
sudo chown -R lklalitesh:_www "$PROJECT_ROOT/storage" "$PROJECT_ROOT/bootstrap/cache"

# Set normal permissions
sudo chmod -R 775 "$PROJECT_ROOT/storage" "$PROJECT_ROOT/bootstrap/cache"

# Add ACLs so _www and lklalitesh always keep rights
sudo chmod -R +a "_www allow read,write,delete,add_file,add_subdirectory,file_inherit,directory_inherit" "$PROJECT_ROOT/storage" "$PROJECT_ROOT/bootstrap/cache"
sudo chmod -R +a "lklalitesh allow read,write,delete,add_file,add_subdirectory,file_inherit,directory_inherit" "$PROJECT_ROOT/storage" "$PROJECT_ROOT/bootstrap/cache"

echo "✅ Permissions fixed!"
