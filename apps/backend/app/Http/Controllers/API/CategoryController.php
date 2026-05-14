<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Service untuk menangani logic kategori
     */
    protected $categoryService;

    /**
     * Inject CategoryService ke controller
     *
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Ambil semua kategori user (opsional filter type)
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Ambil kategori milik user berdasarkan type jika ada
        $categories = $this->categoryService->getUserCategories(
            $request->user(),
            $request->type
        );
        
        return CategoryResource::collection($categories);
    }

    /**
     * Tampilkan detail kategori
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        // Validasi hak akses user
        $this->authorize('view', $category);

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Simpan kategori baru
     *
     * @param CategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        // Buat kategori baru via service
        $category = $this->categoryService->createCategory(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category)
        ], 201);
    }

    /**
     * Update data kategori
     *
     * @param CategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        // Validasi hak akses user
        $this->authorize('update', $category);

        // Update kategori via service
        $updated = $this->categoryService->updateCategory(
            $category,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($updated)
        ]);
    }

    /**
     * Hapus kategori jika tidak memiliki transaksi
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        // Validasi hak akses user
        $this->authorize('delete', $category);
        
        // Cek apakah kategori masih memiliki transaksi
        if ($category->transactions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has transactions'
            ], 409);
        }
        
        // Hapus kategori
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}