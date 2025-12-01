[![License](https://img.shields.io/github/license/vlauciani/ws-icon.svg)](https://github.com/vlauciani/ws-icon/blob/main/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/vlauciani/ws-icon.svg)](https://github.com/vlauciani/ws-icon/issues)

[![Docker build](https://img.shields.io/badge/docker%20build-from%20CI-yellow)](https://hub.docker.com/r/vlauciani/ws-icon)
![Docker Image Size (latest semver)](https://img.shields.io/docker/image-size/vlauciani/ws-icon?sort=semver)
![Docker Pulls](https://img.shields.io/docker/pulls/vlauciani/ws-icon)

[![CI](https://github.com/vlauciani/ws-icon/actions/workflows/docker-image.yml/badge.svg)](https://github.com/vlauciani/ws-icon/actions)
[![GitHub](https://img.shields.io/static/v1?label=GitHub&message=Link%20to%20repository&color=blueviolet)](https://github.com/vlauciani/ws-icon)

# ws-icon

Docker container per far partire un servizio per generare le icone.

## Quick start
To *starts* docker container you have two options:
### 1) from registry (*preferred*)
```sh
docker pull vlauciani/ws-icon:latest
docker run -d --rm -p 8999:80 --name ws-icon__container vlauciani/ws-icon:latest
```

### 2) by yourself
```sh
git clone https://github.com/vlauciani/ws-icon.git
cd ws-icon
docker build -t vlauciani/ws-icon:latest .
docker run -d --rm -p 8999:80 --name ws-icon__container vlauciani/ws-icon:latest
```

## Environment variables
Set *environment* variables:
- `LOG_ENABLED`: `true`/`false`. Enable or disable log. *Default*: `false`
- `LOG_TO_STDOUT`: `true`/`false`. Redirect output log to *stdout*; works only with `LOG_ENABLED=true`. *Default*: `true`
- `LOG_FILE`: log filename; works only with `LOG_ENABLED=true` and `LOG_TO_STDOUT=false`. *Default*: `/tmp/log/YYYY-MM-DD__ws_icon.log`

## Run example
Run:
```sh
docker run -d --rm -p 8999:80 -e LOG_ENABLED=true -e LOG_TO_STDOUT=true --name ws-icon__container vlauciani/ws-icon:latest
```
or
```sh
docker run -d --rm -p 8999:80 -e LOG_ENABLED=true --name ws-icon__container vlauciani/ws-icon:latest
```

and connect to:
- http://localhost:8999/icon.php?type=circle&xsize=120&ysize=120&bgcolor=FF0000&label=ba&textcolor=000000
- http://localhost:8999/swagger/ (API Documentation)
- http://localhost:8999/health.php (Health Check)

## API Documentation

Full interactive API documentation is available via Swagger UI at:
```
http://localhost:8999/swagger/
```

## API Examples

### Triangle Icons
Triangle icons with customizable background, label, border color, and font size:
```
http://localhost:8999/icon.php?type=triangle&bgcolor=FF0000&label=T1&labelcolor=FFFFFF&bordercolor=000000&fontsize=10
http://localhost:8999/icon.php?type=triangle&bgcolor=00FF00&label=A&labelcolor=000000&bordercolor=FFFF00&fontsize=12
```

### Pentagon Icons
Pentagon icons with customizable background, label, border color, and font size:
```
http://localhost:8999/icon.php?type=pentagon&bgcolor=FFA500&label=P1&labelcolor=FFFFFF&bordercolor=000000&fontsize=12
http://localhost:8999/icon.php?type=pentagon&bgcolor=FF6347&label=PT&labelcolor=FFFFFF&bordercolor=8B0000&fontsize=10
```

### Hexagon Icons
Hexagon icons with customizable background, label, border color, and font size:
```
http://localhost:8999/icon.php?type=hexagon&bgcolor=800080&label=H1&labelcolor=FFFFFF&bordercolor=000000&fontsize=12
http://localhost:8999/icon.php?type=hexagon&bgcolor=4169E1&label=HX&labelcolor=FFFFFF&bordercolor=000080&fontsize=10
```

### Star Icons
5-pointed star icons with customizable background, label, border color, and font size:
```
http://localhost:8999/icon.php?type=star&bgcolor=FFD700&label=S1&labelcolor=000000&bordercolor=FF8C00&fontsize=12
http://localhost:8999/icon.php?type=star&bgcolor=FFA500&label=ST&labelcolor=FFFFFF&bordercolor=8B0000&fontsize=10
```

### Circle Icons (circle, or deprecated event1)
Circular icons with custom radius, background color, label text, and text color:
```
http://localhost:8999/icon.php?type=circle&xsize=60&ysize=60&bgcolor=0000FF&label=C1&textcolor=FFFFFF
http://localhost:8999/icon.php?type=circle&xsize=80&ysize=80&bgcolor=FF00FF&label=ev&textcolor=000000

# Deprecated (use 'circle' instead):
http://localhost:8999/icon.php?type=event1&xsize=60&ysize=60&bgcolor=0000FF&label=C1&textcolor=FFFFFF
```

### Square Icons (square, or deprecated sta2)
Square icons with border representing database status color:
```
http://localhost:8999/icon.php?type=square&label=ST&labelcolor=FFFFFF&bgcolor=FF0000&dbstatuscolor=00FF00
http://localhost:8999/icon.php?type=square&label=XY&labelcolor=000000&bgcolor=FFFF00&dbstatuscolor=0000FF&bordersize=5

# Deprecated (use 'square' instead):
http://localhost:8999/icon.php?type=sta2&label=ST&labelcolor=FFFFFF&bgcolor=FF0000&dbstatuscolor=00FF00
```

## New Features (v2.0)

### Enhanced Validation
- Hex color validation (must be 6-digit hexadecimal)
- Size limits: 10-500 pixels for circle radius
- Font size limits: 6-72 points
- Border size limits: 0-10 pixels
- Label length limits: 1-10 characters

### Improved Error Responses
Errors are now returned as JSON with detailed validation information:
```json
{
  "error": true,
  "code": 400,
  "message": "Invalid parameters",
  "validation_errors": [
    {
      "parameter": "bgcolor",
      "message": "Invalid hex color format",
      "expected": "6-digit hexadecimal color (e.g., FF0000)",
      "value": "ZZZ"
    }
  ],
  "timestamp": "2025-11-29T10:30:00 UTC"
}
```

### CORS Support
Cross-Origin Resource Sharing (CORS) is enabled for all endpoints, allowing browser-based applications to use the API.

### Enhanced Response Headers
All icon responses include helpful headers:
- `X-Cache-Status`: `HIT` or `MISS` (indicates cache usage)
- `X-Icon-Type`: Canonical type name
- `X-Deprecated-Type`: (Only for deprecated types) Shows which deprecated type was used
- `X-Preferred-Type`: (Only for deprecated types) Suggests the preferred type
- `ETag`: MD5 hash for browser caching
- `Cache-Control`: Aggressive caching (1 year) for immutable icons

### Health Check Endpoint
Monitor service health at:
```
http://localhost:8999/health.php
```

Returns JSON with status and system checks:
```json
{
  "status": "healthy",
  "timestamp": "2025-11-29T10:30:00Z",
  "version": "1.0.0",
  "checks": {
    "icons_directory_writable": true,
    "gd_library": true,
    "fonts_available": true
  }
}
```

### Type Deprecation
- `sta2` is deprecated in favor of `square` (backwards compatible)
- `event1` is deprecated in favor of `circle` (backwards compatible)
- Deprecated types will continue to work but include deprecation headers

### Enhanced Logging
When logging is enabled, logs now include:
- Cache hit/miss status
- Request duration in milliseconds
- Client IP address
- Deprecation warnings

### Refactored Architecture
The codebase has been refactored for better maintainability:
- `server/icon.php`: Entry point and request router (backward compatibility)
- `server/square.php`, `server/circle.php`, `server/triangle.php`, `server/pentagon.php`, `server/hexagon.php`, `server/star.php`: Direct RESTful entry points
- `server/types/`: Type-specific handlers (TypeSquare.php, TypeCircle.php, TypeTriangle.php, TypePentagon.php, TypeHexagon.php, TypeStar.php)
- `server/functions.php`: Common utilities and image generation functions
- `server/swagger/`: OpenAPI specification and Swagger UI

## Export log files to your *host* from *container*
If you set `LOG_ENABLED=true` and `LOG_TO_STDOUT=false` and you want to *view* your log files outside the *container*, create `ws-icon_log` directory with *full* permissions:
```sh
mkdir /tmp/ws-icon_log
chmod 777 /tmp/ws-icon_log/
```

and *mount* it with: `-v /tmp/ws-icon_log:/tmp/log`. Example:
```sh
docker run -d --rm -p 8999:80 -e LOG_ENABLED=true -e LOG_TO_STDOUT=false -v /tmp/ws-icon_log:/tmp/log --name ws-icon__container vlauciani/ws-icon:latest
```

## Develop
To develop, run:
```sh
git clone https://github.com/vlauciani/ws-icon.git
cd ws-icon
docker build -t vlauciani/ws-icon:latest .
docker run --rm -p 8999:80 -e LOG_ENABLED=true -e LOG_TO_STDOUT=true --name ws-icon__container -v $(pwd)/log:/tmp/log -v $(pwd):/app vlauciani/ws-icon:latest
```

# Contribute
Thanks to your contributions!

Here is a list of users who already contributed to this repository: \
<a href="https://github.com/vlauciani/ws-icon/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=vlauciani/ws-icon" />
</a>

# Author
(c) 2025 Valentino Lauciani vlauciani[at]gmail.com