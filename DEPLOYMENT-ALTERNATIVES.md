# 🚀 Laravel EC 無料デプロイ代替案

Railway無料版での制限を回避するための代替サービス一覧です。

## 🆓 完全無料サービス（クレジットカード不要）

### 1. **Render.com** (推奨)
- **RAM**: 512MB
- **制限**: 750時間/月 (約31日)
- **休止**: 15分後に自動休止
- **特徴**: PostgreSQL無料、HTTPS対応
- **URL**: https://render.com/

### 2. **Koyeb.com**
- **RAM**: 256MB
- **制限**: 無制限稼働
- **休止**: 1時間後に自動休止
- **特徴**: フランクフルト拠点、DDoS保護
- **URL**: https://koyeb.com/

### 3. **Glitch.com**
- **RAM**: 512MB
- **制限**: 1000時間/月
- **休止**: 5分後に自動休止
- **特徴**: エディター内蔵、プロジェクト共有
- **URL**: https://glitch.com/

## 💳 クレジットカード必要（初期無料）

### 4. **Fly.io**
- **RAM**: 256MB
- **制限**: 3台まで無料
- **特徴**: グローバル展開、高性能
- **URL**: https://fly.io/

## 🛠️ 推奨移行手順

### Render.comへの移行
```bash
# 1. Render用設定作成
echo "web: cd src && php artisan serve --host=0.0.0.0 --port=\$PORT" > Procfile.render

# 2. render.yaml作成
cat > render.yaml << 'EOF'
services:
  - type: web
    name: laravel-ec
    env: php
    buildCommand: cd src && composer install --no-dev
    startCommand: cd src && php artisan serve --host=0.0.0.0 --port=$PORT
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
EOF

# 3. GitHubプッシュ
git add .
git commit -m "Add Render.com configuration"
git push origin main
```

### Koyeb.comへの移行
```bash
# 1. koyeb.yaml作成
cat > koyeb.yaml << 'EOF'
name: laravel-ec
services:
  - name: web
    git:
      branch: main
    instance_type: nano
    ports:
      - port: 8000
        protocol: http
    env:
      - key: APP_ENV
        value: production
    build:
      buildpack: php
EOF
```

## 📊 サービス比較

| サービス | RAM | 制限 | 休止時間 | DB | HTTPS |
|---------|-----|------|---------|-----|-------|
| Render | 512MB | 750h/月 | 15分 | ✅ | ✅ |
| Koyeb | 256MB | 無制限 | 1時間 | ❌ | ✅ |
| Glitch | 512MB | 1000h/月 | 5分 | ❌ | ❌ |
| Fly.io | 256MB | 3台まで | なし | ✅ | ✅ |

## 🎯 最適な選択

**Laravel EC サイトには Render.com を推奨**
- 十分なRAM（512MB）
- PostgreSQL無料付帯
- 本格的なWebアプリに適している
- 設定が簡単

移行手順はいつでもサポートします！ 🤝 