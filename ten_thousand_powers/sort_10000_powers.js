// Generating such numbers in the js using Math would result in Infinity in the array of numbers
// A quick way if we are not worried about the result just yet is to sort the array of numbers that make up the result of a^b
// But let's assume you have the numbers from an external source.
//    You may convert the number to an exponent with .toExponential()
//    (done automatically in node when assigned to an array) and sort using quick sort. By my tests,
//    I got ~369 milliseconds for an array of 10,000 entries


const pivot = (arr, start = 0, end = arr.length + 1) => {
  const swap = (list, a, b) => [list[a], list[b]] = [list[b], list[a]];
  
  let pivot = arr[start], pointer = start;
  
  for (let i = start; i < arr.length; i++) {
    if (arr[i] < pivot  ) {
      pointer++;
      swap(arr, pointer, i);
    }
  }
  swap(arr, start, pointer);
  
  return pointer;
}

const quickSort = (arr, start = 0, end = arr.length) => {
  let pivotIndex = pivot(arr, start, end);
  
  if (start >= end) return arr;
  quickSort(arr, start, pivotIndex);
  quickSort(arr, pivotIndex + 1, end);
  
  return arr;
};

const genNumbers = (min, max) => {
  let arr = [];
  for (i = 1; i < max; i++) {
    arr.push(Math.floor(Math.random() * (max - min) + min));
  }
  return arr;
}

// just my utility benchmark function
let benchmark = function(desc) {
  let start = new Date();
  return {
    stop: function() {
      let end  = new Date();
      let time = end.getTime() - start.getTime();
      console.log('Benchmark:', desc, 'finished in', time, 'ms');
    }
  }
};

let numbArr = genNumbers(100, 10000);
let t = benchmark('Sort function');
console.log(quickSort(numbArr));
t.stop();
