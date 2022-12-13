<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Type\Integer;
use Exception;

class BookController extends Controller
{

    public function index()
    {
        $books = Book::orderBy('title', 'asc')->get();
        return $this->getResponse200($books);
    }

    public function getById($id){
        $book = Book::with('authors','category','editorial')->find($id);
        return [
            "status" => true,
            "message" => "Successfull query",
            "data" => $book
        ];
    }

    public function store(Request $request)
    {
        try {
            $isbn = preg_replace('/\s+/', '\u0020', $request->isbn); //Remove blank spaces from ISBN
            $existIsbn = Book::where("isbn", $isbn)->exists(); //Check if a registered book exists (duplicate ISBN)
            if (!$existIsbn) { //ISBN not registered
                $book = new Book();
                $book->isbn = $isbn;
                $book->title = $request->title;
                $book->description = $request->description;
                $book->published_date = date('y-m-d h:i:s'); //Temporarily assign the current date
                $book->category_id = $request->category["id"];
                $book->editorial_id = $request->editorial["id"];
                $book->save();
                foreach ($request->authors as $item) { //Associate authors to book (N:M relationship)
                    $book->authors()->attach($item);
                }
                return $this->getResponse201('book', 'created', $book);
            } else {
                return $this->getResponse500(['The isbn field must be unique']);
            }
        } catch (Exception $e) {
            return $this->getResponse500([]);
        }
    }

    public function update(Request $request, $id){
        $response = ["error" => false, "message" => "Your book has been updated!", "data" => []];
        $book = Book::find($id);
        if($book){
            $isbnOwner = Book::class('isbn', $request->isbn)->first();
            if(!$isbnOwner || $isbnOwner->id == $book->id){
                $book->isbn = trim($request->isbn);
                $book->title = trim($request->title);
                $book->description = trim($request->description);
                $book->category_id = trim($request->category['id']);
                $book->editorial_id = trim($request->editorial_id);
                $book->publish_date = Carbon::now();
                $book->update();
                //delete
                foreach($book->authors as $item){
                    $book->authors()->detach($item->id);
                }
                //add
                foreach($request->authors as $item){
                    $book->authors()->attach($item);
                }
                $response["data"] = $book;
            }else{
                $request["error"] = true;
                $request["message"] = "ISBN duplicated";
            }
        }else{
            $response["error"] = true;
            $response["message"] = "404 not found";
        }
        return $response;
    }




}
