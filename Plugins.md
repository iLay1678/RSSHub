<p align="center">
<img src="https://i.imgur.com/NZpRScX.png" alt="RSSHub" width="100">
</p>
<h1 align="center">RSSHub PHP Ver.</h1>

> 🍰 万物皆可 RSS

RSSHub 是一个轻量、易于扩展的 RSS 生成器, 可以给任何奇奇怪怪的内容生成 RSS 订阅源
# 通用参数

## 内容过滤

### 选出想要的内容 支持正则

 - filter: 过滤标题和描述

 - filter_title: 过滤标题

 - filter_description: 过滤描述
 
如 `/novel/biquge/79_79883?filter=一拳|王八`
### 去掉不要的内容 支持正则
 - filterout: 过滤标题和描述
 - filterout_title: 过滤标题
 - filterout_description: 过滤描述
 
如 `/novel/biquge/79_79883?filterout=一拳|王八`

## 内容匹配
### 指定输出内容
 - regular :使用正则规定输出内容
 
例如 ：`/blog/cross/ifking.cn?regular=<p>(.*?)<\/p>`

## 条数限制
可以使用 limit 参数限制最大条数

例如：`/novel/biquge/79_79883?limit=3`

## 输出 Telegram 即时预览链接
可以输出 Telegram 可识别的即时预览链接, 主要用于文章类 RSS

Telegram 即时预览模式需要在官网制作页面处理模板，请前往[官网](https://instantview.telegram.org/)了解更多

tgiv: 模板 hash，可从模板制作页面分享出来的链接末尾获取（&rhash=后面跟着的字符串）

例如: `/novel/biquge/79_79883&tgiv=0b007ea764bc84`

# 列表

一个十分寒酸的支持列表

若无符合请求路由, 请求将会返回一个奇奇怪怪的 RSS 错误页.

## 小说·文学·阅读

### 笔趣阁

#### 小说更新

- 作者: iLay1678
- 路径: `/novel/biquge/:id`
  - 示例: `/novel/biquge/79_79883`
- 参数说明:
  - id:
    - 小说 id, 可在对应小说页 URL 中找到
    - 提示：由于笔趣阁网站有多个, 各站点小说对应的小说 id 不同. 此 feed 只对应在[`www.biquge5200.com`](https://www.biquge5200.com/)中的小说 id.


## 社交媒体

### 开播提醒

#### 斗鱼直播

- 作者: iLay1678
- 路径: `/live/douyu/:id`
  - 示例: `/live/douyu/1126960`
- 参数说明:
  - id:
    - 直播间 id, 可从直播间 URL 中获取

### 哔哩哔哩

#### 番剧

- 作者: LoliLin
- 路径: `/bilibili/bangumi/:sessionID`
  - 示例: `/bilibili/bangumi/2267573`
- 参数说明:
  - sessionID:
    - 番剧 id, 番剧主页打开控制台执行 `window.__INITIAL_STATE__.ssId` 或 `window.__INITIAL_STATE__.mediaInfo.param.season_id` 获取

#### 投稿

- 作者: LoliLin
- 路径: `/bilibili/user/video/:uid`
  - 示例: `/bilibili/user/video/2267573`
- 参数说明:
  - uid:
    - 用户 id, UP 主空间 URL 中获取

#### 动态

- 作者: LoliLin
- 路径: `/bilibili/user/dynamic/:uid`
  - 示例: `/bilibili/user/dynamic/2267573`
- 参数说明:
  - uid:
    - 用户 id, UP 主空间 URL 中获取
