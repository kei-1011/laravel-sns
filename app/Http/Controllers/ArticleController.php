<?php

namespace App\Http\Controllers;

use App\Article;
use App\Tag;
use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Request;

class ArticleController extends Controller {

    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article');
    }

    /**
     * 記事一覧画面
     */
    public function index() {
        $articles = Article::all()->sortByDesc('created_at');

        return view('articles.index', compact('articles'));

        // return view('articles.index', ['articles' => $articles]);
    }

    /**
     * 新規投稿画面
     */
    public function create() {
        $allTagNames = Tag::all()->map(function($tag) {
            return['text' => $tag->name];
        });
        return view('articles.create',[
            'allTagNames' => $allTagNames,
        ]);
    }

    /**
     * 新規投稿アクション
     */
    public function store(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());
        $article->user_id = $request->user()->id;
        $article->save();

        //コレクションの各要素に対して順に処理
        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });
        return redirect()->route('articles.index');
    }

    /**
     * 記事更新画面
     */
    public function edit(Article $article)
    {
        $tagNames = $article->tags->map(function ($tag) {
            return ['text' => $tag->name];
        });

        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.edit', [
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
    }

    /**
     * 記事更新アクション
     */
    public function update(ArticleRequest $request,Article $article)
    {
        $article->fill($request->all())->save();

        // 更新対象の記事とタグの紐付けをいったん全削除
        $article->tags()->detach();
        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });
        return redirect()->route('articles.index');
    }

    /**
     * 記事削除アクション
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    /**
     * 記事詳細画面
     */
    public function show(Article $article)
    {
        return view('articles.show', ['article' => $article]);
    }

    /**
     * いいねアクション
     */
    public function like(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    /**
     * いいね解除アクション
     */
    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }
}
