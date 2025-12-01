[![License](https://img.shields.io/github/license/vlauciani/ws-icon.svg)](https://github.com/vlauciani/ws-icon/blob/main/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/vlauciani/ws-icon.svg)](https://github.com/vlauciani/ws-icon/issues)
[![Docker Pulls](https://img.shields.io/docker/pulls/vlauciani/ws-icon)](https://hub.docker.com/r/vlauciani/ws-icon)
[![CI](https://github.com/vlauciani/ws-icon/actions/workflows/docker-build-push.yml/badge.svg)](https://github.com/vlauciani/ws-icon/actions)

# ws-icon

A lightweight web service for generating customizable icon images in various shapes (circle, square, triangle, pentagon, hexagon, star) with PNG output.

## Quick Start

### Run from Docker Hub (recommended)
```bash
docker pull vlauciani/ws-icon:latest
docker run -d --rm -p 8999:80 --name ws-icon vlauciani/ws-icon:latest
```

Access the service at:
- **API Documentation:** http://localhost:8999/
- **Health Check:** http://localhost:8999/health.php
- **Example:** http://localhost:8999/icon.php?type=circle&xsize=120&ysize=120&bgcolor=FF0000&label=C1&textcolor=FFFFFF

### Build Yourself
```bash
git clone https://github.com/vlauciani/ws-icon.git
cd ws-icon
docker build -t vlauciani/ws-icon:latest .
docker run -d --rm -p 8999:80 --name ws-icon vlauciani/ws-icon:latest
```

## Supported Icon Types

| Type | Description | Example |
|------|-------------|---------|
| `circle` | Circular icons | [Try it](http://localhost:8999/icon.php?type=circle&xsize=60&ysize=60&bgcolor=0000FF&label=C1&textcolor=FFFFFF) |
| `square` | Square icons with border | [Try it](http://localhost:8999/icon.php?type=square&label=ST&labelcolor=FFFFFF&bgcolor=FF0000&dbstatuscolor=00FF00) |
| `triangle` | Triangular icons | [Try it](http://localhost:8999/icon.php?type=triangle&bgcolor=FF0000&label=T1&labelcolor=FFFFFF&bordercolor=000000&fontsize=10) |
| `pentagon` | Pentagon icons | [Try it](http://localhost:8999/icon.php?type=pentagon&bgcolor=FFA500&label=P1&labelcolor=FFFFFF&bordercolor=000000&fontsize=12) |
| `hexagon` | Hexagon icons | [Try it](http://localhost:8999/icon.php?type=hexagon&bgcolor=800080&label=H1&labelcolor=FFFFFF&bordercolor=000000&fontsize=12) |
| `star` | 5-pointed star icons | [Try it](http://localhost:8999/icon.php?type=star&bgcolor=FFD700&label=S1&labelcolor=000000&bordercolor=FF8C00&fontsize=12) |

## Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `LOG_ENABLED` | `false` | Enable/disable logging |
| `LOG_TO_STDOUT` | `true` | Output logs to stdout (works only if `LOG_ENABLED=true`) |
| `LOG_FILE` | `/tmp/log/YYYY-MM-DD__ws_icon.log` | Log file path (when `LOG_TO_STDOUT=false`) |

**Example with logging:**
```bash
docker run -d --rm -p 8999:80 \
  -e LOG_ENABLED=true \
  -e LOG_TO_STDOUT=true \
  --name ws-icon \
  vlauciani/ws-icon:latest
```

## Key Features

- **Multi-shape support:** Circle, square, triangle, pentagon, hexagon, star
- **Customizable:** Colors, labels, borders, font sizes
- **Validation:** Input validation with detailed error messages (JSON format)
- **Caching:** Aggressive caching (1 year) for optimal performance
- **CORS enabled:** Ready for browser-based applications
- **Health monitoring:** Built-in health check endpoint
- **Swagger UI:** Interactive API documentation

### Validation Limits
- Hex colors: 6-digit hexadecimal (e.g., `FF0000`)
- Circle radius: 10-500 pixels
- Font size: 6-72 points
- Border size: 0-10 pixels
- Label: 1-10 characters

## Development

Run with live reload:
```bash
docker build -t vlauciani/ws-icon:latest .
docker run --rm -p 8999:80 \
  -e LOG_ENABLED=true \
  -e LOG_TO_STDOUT=true \
  -v $(pwd):/app \
  --name ws-icon \
  vlauciani/ws-icon:latest
```

## API Documentation

Full interactive API documentation is available at: **http://localhost:8999/**

The legacy Swagger endpoint is still accessible at: http://localhost:8999/swagger/

## Contribute

Contributions are welcome! See the list of contributors:

<a href="https://github.com/vlauciani/ws-icon/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=vlauciani/ws-icon" />
</a>

## License

[MIT License](LICENSE) - Copyright (c) 2025 Valentino Lauciani

## Author

Valentino Lauciani - vlauciani[at]gmail.com
