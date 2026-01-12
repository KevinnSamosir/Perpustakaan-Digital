<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Statistics
        $stats = [
            'total_books' => Book::count(),
            'available_books' => Book::where('status', 'available')->count(),
            'total_members' => Member::count(),
            'active_members' => Member::where('status', 'active')->count(),
            'active_loans' => Loan::whereNull('return_date')->count(),
            'overdue_loans' => Loan::whereNull('return_date')
                ->where('due_date', '<', now())
                ->count(),
            'total_fines' => Loan::whereBetween('created_at', [$startDate, $endDate])
                ->sum('fine_amount'),
        ];

        // Popular books
        $popularBooks = Book::withCount(['loans' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderBy('loans_count', 'desc')
            ->take(5)
            ->get();

        // Active members
        $activeMembers = Member::withCount(['loans' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderBy('loans_count', 'desc')
            ->take(5)
            ->get();

        // Overdue loans
        $overdueLoans = Loan::with(['member', 'book'])
            ->whereNull('return_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->take(10)
            ->get();

        // Chart data - Loan trends
        $loanTrends = $this->getLoanTrends($startDate, $endDate);
        
        // Chart data - Category distribution
        $categoryDistribution = $this->getCategoryDistribution();

        $chartData = [
            'loan_trends' => $loanTrends,
            'category_distribution' => $categoryDistribution,
        ];

        return view('admin.reports.index', compact(
            'stats',
            'popularBooks',
            'activeMembers',
            'overdueLoans',
            'chartData'
        ));
    }

    private function getLoanTrends($startDate, $endDate)
    {
        $loans = Loan::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            $data[] = $loans->where('date', $dateStr)->first()->count ?? 0;
            $current->addDay();
        }

        // If too many data points, aggregate by week or month
        if (count($labels) > 31) {
            return $this->aggregateByMonth($startDate, $endDate);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function aggregateByMonth($startDate, $endDate)
    {
        $loans = Loan::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $data = [];

        foreach ($loans as $loan) {
            $labels[] = Carbon::create($loan->year, $loan->month)->format('M Y');
            $data[] = $loan->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getCategoryDistribution()
    {
        $categories = Category::withCount('books')
            ->having('books_count', '>', 0)
            ->orderBy('books_count', 'desc')
            ->take(5)
            ->get();

        $otherCount = Book::whereNotIn('category_id', $categories->pluck('id'))->count();

        $labels = $categories->pluck('name')->toArray();
        $data = $categories->pluck('books_count')->toArray();

        if ($otherCount > 0) {
            $labels[] = 'Lainnya';
            $data[] = $otherCount;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function exportPdf(Request $request)
    {
        // TODO: Implement PDF export
        return redirect()->back()->with('error', 'Fitur export PDF belum tersedia');
    }

    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export
        return redirect()->back()->with('error', 'Fitur export Excel belum tersedia');
    }
}
