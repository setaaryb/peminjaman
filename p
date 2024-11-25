        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }
        .item {
            flex: 1 1 calc(33.333% - 20px);
            box-sizing: border-box;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .item-info h2 {
            font-size: 1.2em;
            margin: 0 0 10px;
        }
        .item-info p {
            margin: 5px 0;
            font-size: 0.9em;
        }
        .item-info button {
            display: block;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            text-align: center;
            transition: background-color 0.3s;
        }
        .item-info button.disabled {
            background-color: #ff8c00;
            color: #fff;
            cursor: not-allowed;
        }
        .item-info button:not(.disabled) {
            background-color: #007bff;
            color: #fff;
        }
        .item-info button:hover:not(.disabled) {
            background-color: #0056b3;
        }
        .return-button {
            background-color: #ff8c00;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            text-align: center;
            margin-top: 10px;
        }
        .return-button:hover {
            background-color: #e07b00;
        }
        .popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .popup-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        .popup-content h2 {
            margin-top: 0;
        }
        .popup-content p {
            margin: 10px 0;
        }
        .popup-content input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .popup-content button {
            background-color: #007bff;
            border: none;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            text-align: center;
        }
        .popup-content button:hover {
            background-color: #0056b3;
        }
        .popup-content .close {
            background-color: #ccc;
            color: #000;
            margin-left: 10px;
        }